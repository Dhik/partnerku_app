<?php

namespace App\Domain\Campaign\Controllers;

use App\Domain\Campaign\BLL\Statistic\StatisticBLLInterface;
use App\Domain\Campaign\Job\ScrapJob;
use App\Domain\Campaign\Models\Campaign;
use App\Domain\Campaign\Models\CampaignContent;
use App\Domain\Campaign\Requests\StatisticRequest;
use App\Domain\Campaign\Service\StatisticCardService;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function __construct(
        protected StatisticBLLInterface $statisticBLL,
        protected StatisticCardService $cardService
    ) {}

    /**
     * Update or create statistic
     */
    public function store(CampaignContent $campaignContent, StatisticRequest $request): JsonResponse
    {
        $this->authorize('updateCampaignContent', CampaignContent::class);

        try {
            $result = $this->statisticBLL->store(
                $campaignContent->campaign_id,
                $campaignContent->id,
                Carbon::now(),
                $request->input('like'),
                $request->input('view'),
                $request->input('comment'),
                $campaignContent->tenant_id,
                null,
                $campaignContent->rate_card
            );

            return response()->json([
                'success' => true,
                'message' => 'Statistics saved successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving manual statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving statistics'
            ], 500);
        }
    }

    /**
     * Refresh single content statistic
     */
    public function refresh(CampaignContent $campaignContent): JsonResponse
    {
        $this->authorize('updateCampaignContent', CampaignContent::class);

        try {
            if (empty($campaignContent->link)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No link available for this content'
                ], 400);
            }

            $result = $this->statisticBLL->scrapData(
                $campaignContent->campaign_id,
                $campaignContent->id,
                $campaignContent->channel,
                $campaignContent->link,
                $campaignContent->tenant_id,
                $campaignContent->rate_card
            );

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to refresh statistics'
                ], 500);
            }

            // Update FYP status if views are above 10000
            if ($result && isset($result['view']) && $result['view'] > 10000) {
                $campaignContent->update(['is_fyp' => 1]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Statistics refreshed successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Error refreshing statistics: ' . $e->getMessage(), [
                'content_id' => $campaignContent->id,
                'link' => $campaignContent->link
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error refreshing statistics'
            ], 500);
        }
    }

    /**
     * Bulk refresh campaign statistics
     */
    public function bulkRefresh(Campaign $campaign): JsonResponse
    {
        $this->authorize('viewCampaignContent', CampaignContent::class);

        try {
            $campaignContents = $campaign->campaignContents()
                ->whereNotNull('link')
                ->whereIn('channel', [
                    'instagram_feed',
                    'tiktok_video',
                    'twitter_post',
                    'youtube_video',
                    'shopee_video'
                ])
                ->get();

            if ($campaignContents->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No content available for refresh'
                ]);
            }

            $successCount = 0;
            $failedCount = 0;
            $errors = [];

            foreach ($campaignContents as $content) {
                try {
                    // Add small delay to avoid rate limiting
                    if ($successCount > 0 || $failedCount > 0) {
                        sleep(2);
                    }

                    $result = $this->statisticBLL->scrapData(
                        $campaign->id,
                        $content->id,
                        $content->channel,
                        $content->link,
                        $content->tenant_id,
                        $content->rate_card
                    );

                    if ($result) {
                        $successCount++;
                        
                        // Update FYP status if views are above 10000
                        if (isset($result['view']) && $result['view'] > 10000) {
                            $content->update(['is_fyp' => 1]);
                        }

                        Log::info("Successfully refreshed content ID: {$content->id}");
                    } else {
                        $failedCount++;
                        $errors[] = "Failed to refresh content: {$content->username}";
                        Log::error("Failed to refresh content ID: {$content->id}");
                    }

                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = "Error refreshing {$content->username}: " . $e->getMessage();
                    Log::error("Exception refreshing content ID: {$content->id}", [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Update campaign summary after bulk refresh
            $this->cardService->recapStatisticCampaign($campaign->id);

            $message = "Bulk refresh completed: {$successCount} succeeded, {$failedCount} failed";
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'stats' => [
                    'success_count' => $successCount,
                    'failed_count' => $failedCount,
                    'total_count' => $campaignContents->count()
                ],
                'errors' => $failedCount > 0 ? $errors : []
            ]);

        } catch (\Exception $e) {
            Log::error('Error in bulk refresh: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Bulk refresh failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store information for card statistic
     */
    public function card(int $campaignId, Request $request): JsonResponse
    {
        $this->authorize('viewCampaignContent', CampaignContent::class);
        
        try {
            $data = $this->cardService->card($campaignId, $request);
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error fetching card data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching statistics'
            ], 500);
        }
    }

    /**
     * Get data for chart
     */
    public function chart(int $campaignId, Request $request): JsonResponse
    {
        $this->authorize('viewCampaignContent', CampaignContent::class);
        
        try {
            $data = $this->statisticBLL->getChartDataCampaign($campaignId, $request);
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error fetching chart data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching chart data'
            ], 500);
        }
    }

    /**
     * Get data for chart detail content
     */
    public function chartDetailContent(int $campaignContentId): JsonResponse
    {
        $this->authorize('viewCampaignContent', CampaignContent::class);

        try {
            $data = $this->statisticBLL->getChartDataCampaignContent($campaignContentId);
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error fetching detail chart data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching chart data'
            ], 500);
        }
    }
    public function refreshCampaignContentsForCurrentMonth(): RedirectResponse
    {
        // Get the start and end dates for the current month
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        // Find campaigns where the start_date is in the current month
        $campaigns = Campaign::whereBetween('start_date', [$startOfMonth, $endOfMonth])->get();

        foreach ($campaigns as $campaign) {
            $campaignContents = $campaign->campaignContents;

            foreach ($campaignContents as $content) {
                $data = [
                    'campaign_id' => $campaign->id,
                    'campaign_content_id' => $content->id,
                    'channel' => $content->channel,
                    'link' => $content->link,
                    'tenant_id' => $content->tenant_id,
                    'rate_card' => $content->rate_card
                ];

                if (!is_null($content->link)) {
                    ScrapJob::dispatch($data);

                    // Retrieve statistics and update is_fyp if view count is above 10000
                    $statistics = $this->statisticBLL->scrapData(
                        $campaign->id,
                        $content->id,
                        $content->channel,
                        $content->link,
                        $content->tenant_id,
                        $content->rate_card
                    );

                    if ($statistics && $statistics['view'] > 10000) {
                        $content->is_fyp = 1;
                        $content->save();
                    }
                }
            }
        }

        return redirect()->back()->with([
            'alert' => 'success',
            'message' => trans('messages.process_ongoing'),
        ]);
    }
}

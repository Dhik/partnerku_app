<?php

use App\Domain\Campaign\Controllers\CampaignContentController;
use App\Domain\Campaign\Controllers\CampaignController;
use App\Domain\Campaign\Controllers\StatisticController;
use App\Domain\Campaign\Controllers\KeyOpinionLeaderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::prefix('admin')
    ->middleware('auth')
    ->group(function () {
        Route::prefix('campaign')
            ->group(function () {
                Route::get('/', [CampaignController::class, 'index'])->name('campaign.index');
                Route::get('/get', [CampaignController::class, 'get'])->name('campaign.get');
                Route::get('/summary', [CampaignController::class, 'getCampaignSummary'])->name('campaign.summary');
                Route::get('/total', [CampaignController::class, 'getCampaignTotal'])->name('campaign.total');
                Route::get('/download', [CampaignController::class, 'downloadVideo'])->name('campaign.download');
                Route::get('/nas', [CampaignController::class, 'listFiles'])->name('campaign.nas');
                Route::get('/titles', [CampaignController::class, 'getCampaignsTitles'])->name('campaign.titles');
                Route::get('/create', [CampaignController::class, 'create'])->name('campaign.create');
                Route::post('/store', [CampaignController::class, 'store'])->name('campaign.store');
                Route::get('/{campaign}/edit', [CampaignController::class, 'edit'])->name('campaign.edit');
                Route::get('/{campaign}/refresh', [CampaignController::class, 'refresh'])->name('campaign.refresh');
                Route::get('/bulk-refresh', [CampaignController::class, 'bulkRefresh'])->name('campaign.bulkRefresh');
                Route::get('/refresh-all', [CampaignController::class, 'refreshAllCampaigns'])->name('campaign.refreshAll');
                Route::put('/{campaign}/update', [CampaignController::class, 'update'])->name('campaign.update');
                Route::get('/{campaign}/show', [CampaignController::class, 'show'])->name('campaign.show');
                Route::get('/{campaign}/statistic', [CampaignContentController::class, 'statistics'])->name('campaign.statistics');
                Route::delete('/{campaign}', [CampaignController::class, 'destroy'])->name('campaign.destroy');
                
            });

        Route::prefix('campaignContent')
            ->group(function () {
                Route::get('/getDataTable/{campaignId}', [CampaignContentController::class, 'getCampaignContentDataTable'])
                    ->name('campaignContent.getDataTable');
                Route::get('/select/{campaignId}', [CampaignContentController::class, 'selectApprovedInfluencer'])
                    ->name('campaignContent.select');
                Route::get('/update-shopee-video-links', [CampaignContentController::class, 'updateAllShopeeVideoLinks']);
                Route::get('/getJson/{campaignId}', [CampaignContentController::class, 'getCampaignContentJson'])
                    ->name('campaignContent.getJson');
                Route::post('/store/{campaignId}', [CampaignContentController::class, 'store'])
                    ->name('campaignContent.store');
                Route::get('/getDataTableForRefresh/{campaignId}', [CampaignContentController::class, 'getCampaignContentDataTableForRefresh'])
                    ->name('campaignContent.getDataTableForRefresh');                
                Route::put('/update/{campaignContent}', [CampaignContentController::class, 'update'])
                    ->name('campaignContent.update');
                Route::get('/update/fyp/{campaignContent}', [CampaignContentController::class, 'updateFyp'])
                    ->name('campaignContent.update.fyp');
                Route::get('/update/deliver/{campaignContent}', [CampaignContentController::class, 'updateDeliver'])
                    ->name('campaignContent.update.deliver');
                Route::get('/payment/deliver/{campaignContent}', [CampaignContentController::class, 'updatePayment'])
                    ->name('campaignContent.update.payment');
                Route::get('/export/{campaign}', [CampaignContentController::class, 'export'])
                    ->name('campaignContent.export');
                Route::get('/downloadTemplate', [CampaignContentController::class, 'downloadTemplate'])
                    ->name('campaignContent.template');
                Route::get('/downloadTemplateKOL', [CampaignContentController::class, 'downloadTemplateKOL'])
                    ->name('campaignContent.template_kol');
                Route::post('/import/{campaign}', [CampaignContentController::class, 'import'])
                    ->name('campaignContent.import');
                Route::post('/import_kol/{campaign}', [CampaignContentController::class, 'import_from_KOL'])
                    ->name('campaignContent.import_kol');
                Route::delete('/{campaignContent}', [CampaignContentController::class, 'destroy'])
                    ->name('campaignContent.destroy');
                    
            });

        
        Route::prefix('kol')
            ->group(function () {
                Route::get('/', [KeyOpinionLeaderController::class, 'index'])->name('kol.index');
                Route::get('/get', [KeyOpinionLeaderController::class, 'get'])->name('kol.get');
                Route::get('/kpi', [KeyOpinionLeaderController::class, 'getKpiData'])->name('kol.kpi');
                Route::get('/select', [KeyOpinionLeaderController::class, 'select'])->name('kol.select');
                Route::get('/chart', [KeyOpinionLeaderController::class, 'chart'])->name('kol.chart');
                Route::get('/average-rate', [KeyOpinionLeaderController::class, 'averageRate'])->name('kol.averageRate');
                Route::get('/create', [KeyOpinionLeaderController::class, 'create'])->name('kol.create');
                Route::get('/create-excel', [KeyOpinionLeaderController::class, 'createExcelForm'])->name('kol.createExcel');
                Route::post('/store', [KeyOpinionLeaderController::class, 'store'])->name('kol.store');
                Route::post('/store-excel', [KeyOpinionLeaderController::class, 'storeExcel'])->name('kol.storeExcel');
                Route::get('/{keyOpinionLeader}', [KeyOpinionLeaderController::class, 'show'])->name('kol.show');
                Route::get('/{keyOpinionLeader}/json', [KeyOpinionLeaderController::class, 'showJson'])->name('kol.showJson');
                Route::get('/{keyOpinionLeader}/edit', [KeyOpinionLeaderController::class, 'edit'])->name('kol.edit');
                Route::get('/{keyOpinionLeader}/edit-data', [KeyOpinionLeaderController::class, 'getEditData'])->name('kol.editData');
                Route::put('/{keyOpinionLeader}', [KeyOpinionLeaderController::class, 'update'])->name('kol.update');
                Route::delete('/{keyOpinionLeader}', [KeyOpinionLeaderController::class, 'destroy'])->name('kol.destroy');
                Route::get('/export/excel', [KeyOpinionLeaderController::class, 'export'])->name('kol.export');
                Route::get('/refresh/{username}', [KeyOpinionLeaderController::class, 'refreshFollowersFollowing'])->name('kol.refresh');
                Route::get('/refresh-single/{username}', [KeyOpinionLeaderController::class, 'refreshFollowersFollowingSingle'])->name('kol.refreshSingle');
                Route::post('/import-google-sheet', [KeyOpinionLeaderController::class, 'importKeyOpinionLeaders'])->name('kol.importGoogle');
                Route::get('/bulk-usernames', [KeyOpinionLeaderController::class, 'getBulkUsernames'])->name('kol.bulkUsernames');
            });

        

        Route::prefix('statistic')
            ->group(function () {
                Route::get('/refresh/{campaignContent}', [StatisticController::class, 'refresh'])
                    ->name('statistic.refresh');

                Route::get('/bulkRefresh/{campaign}', [StatisticController::class, 'bulkRefresh'])
                    ->name('statistic.bulkRefresh');

                Route::get('/card/{campaignId}', [StatisticController::class, 'card'])
                    ->name('statistic.card');

                Route::get('/chart/{campaignId}', [StatisticController::class, 'chart'])
                    ->name('statistic.chart');
                Route::get('/chart-detail/{campaignContentId}', [StatisticController::class, 'chartDetailContent'])
                    ->name('statistic.chartDetail');

                Route::post('/{campaignContent}', [StatisticController::class, 'store'])
                    ->name('statistic.store');

            });
    });

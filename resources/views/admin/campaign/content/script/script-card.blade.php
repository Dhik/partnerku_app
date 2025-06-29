<script>
    function updateCard() {
    $.ajax({
        url: "{{ route('statistic.card', ['campaignId' => ':campaignId']) }}".replace(':campaignId', {{ $campaign->id }}) + '?filterDates=' + filterDates.val(),
        method: 'GET',
        success: function(response) {
            console.log("Response from server:", response);
            // Update card elements
            updateElement('#totalExpense', response.total_expense);
            updateElement('#totalCPM', response.cpm);
            updateElement('#cpmBenchmark', response.cmp_benchmark);
            updateElement('#totalInfluencer', response.total_influencer);
            updateElement('#totalContent', response.total_content);
            updateElement('#totalAchievement', response.achievement);
            updateElement('#totalViews', response.view);
            updateElement('#totalLikes', response.like);
            updateElement('#totalComment', response.comment);
            updateElement('#engagementRate', response.engagement_rate);

            // Update CPM comparison
            updateCPMComparison(response.cpm, response.cmp_benchmark);

            // Update tables
            updateTable('top-likes-table', response.top_likes, 'like');
            updateTable('top-comments-table', response.top_comment, 'comment');
            updateTable('top-views-table', response.top_view, 'view');
            updateTable('top-engagements-table', response.top_engagement, 'engagement');

            updateTableProduct(response.top_product)
        },
        error: function(xhr, status, error) {
            console.error('Error fetching card', error);
        }
    });
}

function updateCPMComparison(actualCPM, benchmarkCPM) {
    // Remove formatting to get numeric values
    const actual = parseFloat(actualCPM.replace(/[.,]/g, function(match) {
        return match === ',' ? '.' : '';
    }));
    const benchmark = parseFloat(benchmarkCPM.replace(/[.,]/g, function(match) {
        return match === ',' ? '.' : '';
    }));
    
    if (benchmark > 0) {
        const percentage = (actual / benchmark) * 100;
        const difference = actual - benchmark;
        const isGood = actual <= benchmark; // Lower CPM is better
        
        // Update progress bar
        $('#cpmProgress').css('width', Math.min(percentage, 100) + '%');
        $('#cpmProgress').removeClass('bg-success bg-warning bg-danger');
        
        if (percentage <= 80) {
            $('#cpmProgress').addClass('bg-success');
        } else if (percentage <= 100) {
            $('#cpmProgress').addClass('bg-warning');
        } else {
            $('#cpmProgress').addClass('bg-danger');
        }
        
        // Update comparison text
        const comparisonText = isGood 
            ? `${Math.abs(difference).toFixed(2)} below benchmark` 
            : `${Math.abs(difference).toFixed(2)} above benchmark`;
        $('#cmpComparison').text(comparisonText);
        $('#cmpComparison').removeClass('text-success text-danger');
        $('#cmpComparison').addClass(isGood ? 'text-success' : 'text-danger');
    }
}

    function updateElement(selector, value) {
        $(selector).text(value);
    }

    function updateTable(tableId, data, dataType) {
        const tableBody = $(`#${tableId} tbody`);
        tableBody.empty();

        $.each(data, function(index, item) {
            const row = $('<tr></tr>');

            const nameCell = item.id ? 
            `<a href="/admin/kol/${item.id}/show">${item.key_opinion_leader_name}</a>` : 
            item.key_opinion_leader_name;

            row.append(`
                <td>${nameCell}</td>
                <td class="text-right">${formatNumber(item[dataType])}</td>
            `);

            tableBody.append(row);
        });
    }

    function updateTableProduct(data) {
        const tableBody = $(`#top-product-table tbody`);
        tableBody.empty();

        $.each(data, function(index, item) {
            const row = $('<tr></tr>');

            row.append(`
                    <td>${item.product}</td>
                    <td class="text-right">${item.total_views}</td>
                    <td class="text-right">${item.total_spend}</td>
                    <td class="text-right">${item.total_content}</td>
                    <td class="text-right">${item.cpm}</td>
                    <td class="text-right">${item.target}</td>
                `);

            tableBody.append(row);
        });
    }

    // Function to format number with thousand separators
    function formatNumber(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
</script>

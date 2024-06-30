

const prcHeaders = {
    'diff1dprc': '1 DAG',
    'diff1yprc': '1 ÅR',
    'diffqtdprc': 'FJÄRDEDEL',
    'diff3mprc': '3 MÅNADER'
};

jQuery(document).ready(function ($) {

    // Function to insert share icon to news blog module
    function insertShareIcon() {
        var shareIcon = '<div class="share-icon"><a href="#" class="share-link"><img src="http://localhost/aktier/wp-content/themes/divi-child/uploads/posts/share-icon.png"/></a></div>';
        var postMetas = $('.category-news>.post-meta').toArray();
        postMetas.forEach(postMeta => {
            $(postMeta).append(shareIcon);
        });
    }

    // Function to insert finwire url to each post title of finwire blog module
    function insertFinwireUrl() {
        var finwires = $('.category-finwire').toArray();
        finwires.forEach(function (finwire) {
            console.log(finwire);
            var link = $(finwire).find('.entry-title a');
            var export_field = $(finwire).find('.post-content .post-content-inner p');
            var url = $(export_field).text();
            link.attr('href', url);
            $(export_field).css("display", "none");
        });
    }

    // Function to insert annons badge to the advertisements blog module
    function insertAnnonseBadge() {
        var ads = $('.category-advertisement').toArray();
        ads.forEach(ad => {
            $(ad).find('.entry-featured-image-url').after('<div><p class="annons-badge">Annons</p></div>');
        })
    }

    function customizeUploadInput() {
        $('.contact-upload-input').after('<label for="file-374">LADDA UPP FILE</label>');

        $('.contact-upload-input').each(function () {
            var label = $(this).next(),
                labelVal = label.html();

            $(this).on('change', function (e) {
                var fileName = '';
                if (this.files && this.files.length > 1)
                    fileName = ($(this).attr('data-multiple-caption') || '').replace('{count}', this.files.length);
                else
                    fileName = e.target.value.split('\\').pop();


                if (fileName)
                    label.html(fileName);
                else
                    label.html(labelVal);
            });
        });

    }

    $(".accordion").on("click", function () {
        var viewportWidth = $(window).width();
        if (viewportWidth < 768) {
            $(this).toggleClass("active");
            var panel = $(this).next();
            panel.slideToggle();
        }
    });

    // Customize Market news blog

    function customizeMarcketNewsBlog() {
        var articles = $('.blog-market-news article');
        articles.each(function () {
            $('.post-meta').each(function () {
                // Remove the comma and space from the text node
                $(this).contents().filter(function () {
                    return this.nodeType === 3 && this.nodeValue.trim() === ',';
                }).remove();
            });

            var postMetas = $(this).find('.post-meta a');
            // console.log(postMetas, 'postMes  ');
            postMetas.each(function () {
                if ($(this).text().includes('Knowledge Bank')) {
                    $(this).remove();
                    var nextElement = $(this).next();
                    if (nextElement.text().trim() === ', ') {
                        nextElement.remove();
                    }
                    var prevElement = $(this).prev();
                    if (prevElement.text().trim() === ', ') {
                        prevElement.remove();
                    }
                }
            });
            var postMeta = $(this).find('.post-meta');
            var entryTitle = $(this).find('.entry-title');
            postMeta.insertBefore(entryTitle);
        });
    }

    function saveStocks() {
        $.ajax({
            type: 'GET',
            url: 'http://localhost/aktier/wp-content/themes/divi-child/ajax-handlers/store-stocks.php',
            data: {
                action: 'fetch_and_store_stocks',
            },
            success: function (response) {
                const res = JSON.parse(response);
                if (res.success)
                    console.info(`Success!: ${res.message}`);
                else
                    console.info(`Error!: ${res.error}`);
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    function saveRealTimeDiscords() {

        // setTimeout(() => {
        console.log('test')
        $.ajax({
            type: 'GET',
            url: 'http://localhost/aktier/wp-content/themes/divi-child/ajax-handlers/store-discords.php',
            data: {
                action: 'store_realtime_discords',
            },
            success: function (response) {
                const res = JSON.parse(response);
                if (res.success)
                    console.info(`Success!: ${res.message}`);
                else
                    console.info(`Error!: ${res.error}`);
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });

    }

    insertShareIcon();
    insertFinwireUrl();
    insertAnnonseBadge();
    customizeUploadInput();
    customizeMarcketNewsBlog();

    setInterval(() => {
        saveStocks();
    }, 3600000)

    // saveRealTimeDiscords();

});

let upStocks = [];
let downStocks = [];
// Define functions to update price elements
function updatePrcs(selector, value) {
    let $element = $(selector);
    let isPositive = value > 0;
    $element.find('img').attr('src', `http://localhost/aktier/wp-content/themes/divi-child/uploads/${isPositive ? 'rising' : 'down'}.png`);
    $element.find('span:last-child').text(`${value} %`);
    $element.find('span:last-child').removeClass('up down').addClass(isPositive ? 'up' : 'down');
}

// Function to fetch and display stocks
function getStocks(url, paged, perPage, searchInput, order, orderby, prc, targetElement, viewPrcsFunction) {
    const stocksArray = targetElement == '#up-stocks-tbody' ? upStocks : downStocks;
    $.ajax({
        type: 'POST',
        url: url,
        data: { action: 'get_stocks', paged, perPage, searchInput, order, orderby },
        success: function (res) {
            if (res.success) {
                let stocks = res.data;
                viewPrcsFunction(stocks[0]);
                let stockTbody = '';
                stocksArray.length = 0;
                Array.prototype.push.apply(stocksArray, stocks);
                stocks.forEach((stock, i) => {
                    const detailPageURL = `http://localhost/aktier/stock/?insref=${stock.insref}`;
                    stockTbody += `<tr id="${stock.id}">`;
                    stockTbody += `<td>${i + 1}</td>`;
                    stockTbody += `<td class="td-name"><a href="${detailPageURL}"><div class="td-name-image"><img src="https://flagcdn.com/32x24/${stock.country.toLowerCase()}.png"></div><div class="td-name-letter">${stock.stock_name}</div></a></td>`;
                    stockTbody += `<td class="${stock[prc] > 0 ? 'up' : 'down'}">${stock[prc]} %</td>`;
                    stockTbody += `<td>${stock.lastprice}</td>`;
                    stockTbody += `<td>${stock.numberofshares}</td>`;
                    stockTbody += '<td class="table-action"><div><div><img src="http://localhost/aktier/wp-content/themes/divi-child/uploads/Vector.png"/></div><div><img src="http://localhost/aktier/wp-content/themes/divi-child/uploads/share_icon.png"/></div></div></td>';
                    stockTbody += '</tr>';
                });
                $(targetElement).html(stockTbody);
            } else {
                console.error(res.error);
                $(targetElement).html(res.error);
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.error('AJAX Error: ' + errorThrown);
        }
    });
}

// Update price elements for up stocks
function viewUpPrcs(stocks) {
    updatePrcs('#up_diff1dprc', stocks.diff1dprc);
    updatePrcs('#up_diff3mprc', stocks.diff3mprc);
    updatePrcs('#up_diffqtdprc', stocks.diffqtdprc);
    updatePrcs('#up_diff1yprc', stocks.diff1yprc);
}

// Update price elements for down stocks
function viewDownPrcs(stocks) {
    updatePrcs('#down_diff1dprc', stocks.diff1dprc);
    updatePrcs('#down_diff3mprc', stocks.diff3mprc);
    updatePrcs('#down_diffqtdprc', stocks.diffqtdprc);
    updatePrcs('#down_diff1yprc', stocks.diff1yprc);
}

$(document).ready(function () {
    const url = ajax_object.ajaxurl;
    let paged = 1;
    let perPageUp = 15;
    let perPageDown = 15;
    let searchInput = '';
    let order = 'DESC';
    let orderby = 'diff1dprc';
    let prcValue = 'diff1dprc';

    $('#down_diff1dprc_btn').addClass('active');
    $('#up_diff1dprc_btn').addClass('active');

    function fetchUpStocks() {
        getStocks(url, paged, perPageUp, searchInput, order, orderby, prcValue, '#up-stocks-tbody', viewUpPrcs);
    }

    function fetchDownStocks() {
        getStocks(url, paged, perPageDown, searchInput, 'ASC', orderby, prcValue, '#down-stocks-tbody', viewDownPrcs);
    }

    fetchUpStocks();
    fetchDownStocks();

    $('#up-search-input, #down-search-input').on('change', function (e) {
        e.preventDefault();
        searchInput = $(this).val();
        if ($(this).attr('id') === 'up-search-input') {
            fetchUpStocks();
        } else {
            fetchDownStocks();
        }
    });

    $('#up_prc_btns a, #down_prc_btns a').click(function () {
        $(this).addClass('active').siblings().removeClass('active');
        prcValue = $(this).attr('id').split("_")[1];
        orderby = prcValue;
        var parentID = $(this).parent().attr('id');
        $('#' + parentID.replace('_prc_btns', '') + '-prc-label').text(prcHeaders[prcValue]);
        if (parentID === 'up_prc_btns') {
            fetchUpStocks();
        } else {
            fetchDownStocks();
        }
    });

    $('#up-view-btn').on('click', function () {
        perPageUp += 15;
        fetchUpStocks();
    });

    $('#down-view-btn').on('click', function () {
        perPageDown += 15;
        fetchDownStocks();
    });

    $(document).on('click', '#down-stocks-tbody tr', function () {
        let curDownStock = downStocks.find(stock => stock.id == $(this).attr('id'));
        viewDownPrcs(curDownStock);
    });

    $(document).on('click', '#up-stocks-tbody tr', function () {
        let curUpStock = upStocks.find(stock => stock.id == $(this).attr('id'));
        viewUpPrcs(curUpStock);
    });
});


//discord


function fetchData(paged = 1, perPage = 10, searchInput = '') {
    $('.loader').show();
    $.ajax({
        type: 'POST',
        url: ajax_object.ajaxurl,
        data: {
            action: 'discord_ajax_search',
            search_term: searchInput,
            paged: paged,
            per_page: perPage
        },
        success: function (response) {
            if (response.success) {
                var discords = response.data.discords;
                console.log(discords, 'discords')

                let discordTbody = '';
                discords.forEach(function (discord, i) {
                    discordTbody += `<tr>`;
                    discordTbody += `<td>${(i + 1)}</td>`;
                    discordTbody += discord.img_src ? `<td class="td-name"><a href="${discord.url}" target="_blank"> <div class="td-name-image"><img style="border-radius: 50%;" src="${discord.img_src}"></div><div class="td-name-letter">${discord.name}</div></a> </td>`
                        : `<td class="td-name"><a href="${discord.url}"> <div class="td-name-image"><img style="border-radius: 50%;" src="http://localhost/aktier/wp-content/themes/divi-child/uploads/question-icon.jpg"></div><div class="td-name-letter">${discord.name}</div></a> </td>`;
                    discordTbody += `<td>${discord.name}</td>`;
                    discordTbody += `<td>${discord.member_count} ${discord.presence_count}</td>`;
                    discordTbody += `<td class="table-action"><div><div><img src="http://localhost/aktier/wp-content/themes/divi-child/uploads/Vector.png"/></div><div><img src="http://localhost/aktier/wp-content/themes/divi-child/uploads/share_icon.png"/></div></div></td>`;
                    discordTbody += `</tr>`;
                });

                $('#discord-tbody').html(discordTbody);

                updatePaginationButtons(paged, response.data.pageInformation.totalPage);
                $('.loader').hide();

            } else {
                $('#discord-tbody').html('<p>' + response.data + '</p>');
                $('.loader').hide();
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            $('.loader').hide();
        }
    });
}

function updatePaginationButtons(currentPage, totalPages) {
    var paginationHTML = '<li class="page-item ' + (currentPage <= 1 ? 'disabled' : '') + '"><a class="page-link" href="#" data-page="' + (currentPage - 1) + '">Previous</a></li>';

    // Determine the range of pages to display
    var startPage = Math.max(1, currentPage - 2);
    var endPage = Math.min(totalPages, currentPage + 2);

    if (startPage > 1) {
        paginationHTML += '<li class="page-item disabled"><a >...</a></li>';
    }

    for (var i = startPage; i <= endPage; i++) {
        paginationHTML += '<li class="page-item ' + (currentPage == i ? 'active' : '') + '"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
    }

    if (endPage < totalPages) {
        paginationHTML += '<li class="page-item disabled"><a >...</a></li>';
    }

    paginationHTML += '<li class="page-item ' + (currentPage >= totalPages ? 'disabled' : '') + '"><a class="page-link" href="#" data-page="' + (currentPage + 1) + '">Next</a></li>';

    $('#pagination-buttons').html(paginationHTML);

    $('#discord-search-form').on('submit', function (e) {
        e.preventDefault();
        fetchData(1, $('.discord-sel-main').val(), $('#discord-search-input').val()); // Update perPage as needed
    });
}

$(document).ready(function () {
    fetchData();
    $('.discord-sel-main').val('10');
    $(document).on('click', '.page-link', function (e) {
        if (!$(this).parent().hasClass('disabled')) {
            var page = parseInt($(this).data('page'));
            var searchInput = $('#discord-search-input').val();
            var per_page = parseInt($('.discord-sel-main').val());
            if (page !== 0 || page !== totalPage) {
                fetchData(page, per_page, searchInput);
            }
        }
    });

    $('#per_page').on('change', function () {
        fetchData(1, $(this).val(), $('#discord-search-input').val());
    });

});












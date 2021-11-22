const data_detail = async ( idMessage, list_url, is_MCP_enabled ) => {
    
    let data = await get_data_detail( 'detail', idMessage, list_url );
    data = JSON.parse( data);
    is_MCP_enabled = JSON.parse( is_MCP_enabled);

    format_detail_page( data ,is_MCP_enabled);
}

const data_relayinfo = async ( idMessage ) => {
    let data = await get_data_detail( 'relay', idMessage);
    data = JSON.parse( data );

}

const format_detail_page = async ( data,is_MCP_enabled) => {

    let data_labels = [];
    let data_texts = [];

    for (var property in data) {
        if ( isNaN(property) )
        if (data.hasOwnProperty(property)) {
            data_labels.push( property );
            data_texts.push( data[property]);
        }
    }
    
    const received_title = data_texts[0] + ' - ' + data_texts[5];
    const received_via = data_labels[8];
    const message_headers = data_labels[9];
    const virustitle = data_texts[33];
    const enginetitle = data_texts[34];
    const spam_report = data_labels[24];
    const mcptitle = data_texts[35];

    console.log(data_labels);
    console.log(data_texts);

    push_card( 'card_detail_1', received_title, data_labels, data_texts, 0, 8);
    push_card( 'card_detail_2', received_via, data_labels, data_texts, 8, 9); 
    push_card( 'card_detail_3', message_headers, data_labels, data_texts, 9, 10);
    push_card( 'card_detail_4', virustitle, data_labels, data_texts, 12, 15);
    push_card( 'card_detail_5', enginetitle, data_labels, data_texts, 16, 24);
    if( data_texts[24].length > 0 )
        push_card( 'card_detail_6', spam_report, data_labels, data_texts, 24, 25);
    if( is_MCP_enabled )
        push_card( 'card_detail_7', mcptitle, data_labels, data_texts, 25, 32);

}

const push_card = (id_card, title_card, data_labels, data_texts, num_init, num_end) => {

    let card_format = `
        <div class="card card-wr" id="${ id_card }">
            <div class="card-header card-header-warriors" id="headingOne">
                <button class="btn card-header-label-btn" data-toggle="collapse" data-target="#${id_card}_target" aria-expanded="true" aria-controls="collapseOne">
                    <i class="fas fa-envelope-open-text"></i>
                    ${ title_card }
                </button>
            </div>
            <div id="${id_card}_target" class="collapse show" aria-labelledby="headingOne" data-parent="#${ id_card }">
                <div class="card-body card-body-warriors">
                    <div class="table-responsive">
                        <table role="table" id="wr-table" class="tablemessageinfo dark-table warrior-tooltable" width="100%" cellspacing="0">
    `;

    for (let i = num_init; i < num_end; i++) {
        if(data_texts[i]!== null && data_texts[i]!== '')
            if(i !== 9 && i !== 8 && i!==24)
                card_format += `<tr class="wr-tabletr"><td class="pt-2 pb-2 pl-2 wr-tableheading">${data_labels[i]}</td><td class="pl-2">${data_texts[i]}</td></tr>`;
            else
            card_format += `<tr class="wr-tabletr"><td>${data_texts[i]}</td></tr>`;

    }

    card_format += `
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('#detail_js').append(card_format);
}




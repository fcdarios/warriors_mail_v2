let data_labels = [];
let data_texts = [];
let is_MCP_enabled;

$('#msg_subject_modal').on('shown.bs.modal', async function (event) {
  const callModal = $(event.relatedTarget);
  const id = callModal.data('id');

  let label_title = document.getElementById('msg_subject_modal_title');

  let data = [];
  data_labels = [];
  data_texts = [];

  data = await get_data_detail('detail', id);

  is_MCP_enabled = await get_data_detail('is_mcp', id);

  is_MCP_enabled = JSON.parse(is_MCP_enabled);

  data = JSON.parse(data);

  for (var property in data) {
    if (isNaN(property))
      if (data.hasOwnProperty(property)) {
        data_labels.push(property);
        data_texts.push(data[property]);
      }
  }
  $('#id_view_1').html(data_texts[0] + ' - ' + data_texts[5]);
  $('#id_view_2').html(data_labels[8]);
  $('#id_view_3').html(data_labels[9]);
  $('#id_view_4').html(data_texts[33]);
  $('#id_view_5').html(data_texts[34]);
  $('#id_view_6').html(data_labels[24]);
  $('#id_view_7').html(data_texts[35]);

  $('#modal-item-select-body').html();
  $("#idSelectDetail").val('view_1');
  push_card_modal(data_labels, data_texts, 0, 8);

  label_title.innerText = data[1] + ' - ' + id;

})

$('#idSelectDetail').on('change', function () {
  const select_value = this.value;

  switch (select_value) {
    case 'view_1':
      push_card_modal(data_labels, data_texts, 0, 8);

      break;
    case 'view_2':
      push_card_modal_via(data_labels, data_texts, 8, 9);

      break;
    case 'view_3':
      push_card_modal(data_labels, data_texts, 9, 10);

      break;
    case 'view_4':
      push_card_modal(data_labels, data_texts, 12, 15);

      break;
    case 'view_5':
      push_card_modal(data_labels, data_texts, 16, 24);

      break;
    case 'view_6':
      if (data_texts[24].length > 0)
        push_card_modal(data_labels, data_texts, 24, 25);
      else
        push_card_empty();
      break;
    case 'view_7':
      if (is_MCP_enabled)
        push_card_modal(data_labels, data_texts, 25, 32);
      else
        push_card_empty();
      break;
    default:
      break;
  }


});

const push_card_empty = () => {

  let card_format = `
                  <div class="table-responsive">
                      <table role="table" id="wr-table" class="tablemessageinfo table-bordered table-warriors-rep dataTable" width="100%" cellspacing="0">
                      <tbody class="table-body-warriors-rep">`;

  card_format += `<tr><td class="p-4">No hay informacion por el momento</td></tr>`;

  card_format += `</tbody>
                      </table>
                  </div>`;
  $('#modal-item-select-body').html(card_format);
}

const push_card_modal = (data_labels, data_texts, num_init, num_end) => {

  let card_format = `
                  <div class="table-responsive">
                      <table role="table" id="wr-table" class="tablemessageinfo table-bordered table-warriors-rep dataTable" width="100%" cellspacing="0">
                      <tbody class="table-body-warriors-rep">`;
  for (let i = num_init; i < num_end; i++) {
    if (data_texts[i] !== null && data_texts[i] !== '')
      if (i !== 9 && i !== 24)
        card_format += `<tr class="p-2 "><td class="table-head-warriors-rep stretchwidth">${data_labels[i]}</td><td>${data_texts[i]}</td></tr>`;
      else
        card_format += `<tr><td class="p-2">${data_texts[i]}</td></tr>`;
  }
  card_format += `</tbody>
                      </table>
                  </div>`;
  $('#modal-item-select-body').html(card_format);
}

const push_card_modal_via = (data_labels, data_texts, num_init, num_end) => {

  let card_format = `<div class="table-responsive">`;
  for (let i = num_init; i < num_end; i++) {
    if (data_texts[i] !== null && data_texts[i] !== '')

      card_format += `${data_texts[i]}`;
  }
  card_format += `</div>`;
  $('#modal-item-select-body').html(card_format);
}


$("#msg_subject_modal").on("hidden.bs.modal", function () {
  let label_title = document.getElementById('msg_subject_modal_title');
  let modal_body = document.getElementById('modal-item-select-body');


  label_title.innerText = '';
  modal_body.innerHTML = '';
});


$('#msg_notification_modal').on('shown.bs.modal', async function (event) {
  const callModal = $(event.relatedTarget);
  const id = callModal.data('id');

  let label_title = document.getElementById('msg_notification_modal_title');

})
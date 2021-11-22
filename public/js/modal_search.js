$('#modal_search').on('shown.bs.modal', async function (event) {

   let input_search = document.getElementById('input-search');
   search_messages( input_search, false );

})

const search_messages = async ( input, btn_modal = true ) => {

   let input_search = document.getElementById('input_search_modal');
   let token = document.querySelector("input[name='token']").value;
   let search_query = input.value;
   let empty = ( search_query === '' ) ? true : false;
   

   if ( !btn_modal ) {
      input_search.value = search_query;
      document.getElementById('input-search').value = '';
   }

   let body_search = document.getElementById('body-search');
   let body_loading_results = document.getElementById('body-loading-results');
   let body_msg_results = document.getElementById('body-msg-results');
   let body_table_results = document.getElementById('body-table-results');
   
   if ( !empty ) {

      body_search.style.display = 'none';
      body_msg_results.style.display = 'none';
      body_table_results.style.display = 'none';
      body_loading_results.style.display = 'block';
      body_table_results.innerHTML = '';

      let data = await get_data_search( search_query, token );
      const { list_messages, length, columns } = data;


      if ( length > 0 ) {
         
         let table_mails = `
            <table id="table-search" class="table table-sm table-striped table-wr table-wr-search">
               <thead>
                  <tr>
                        <th class="text-center">#</th>
                        <th>${ columns[0] }</th>
                        <th>${ columns[1] }</th>
                        <th>${ columns[2] }</th>
                        <th>${ columns[3] }</th>
                  </tr>
               </thead>
               <tbody>
         `;

         list_messages.forEach( row => {
            let link = `<a href="detail.php?token=${token}&amp;id=${ row[0] }"><i class="fas fa-info-circle"></i></a>`;

            table_mails += `
            <tr>
               <td class="text-center">${ link }</td>
               <td class="td-datetime">${ row[1] }</td>
               <td class="td-search">${ row[2] }</td>
               <td class="td-search">${ row[3] }</td>
               <td class="td-search">${ row[4] }</td>
            </tr>
            `;

         });

         table_mails += `
            </tbody>
            </table>
         `;

         body_table_results.innerHTML = table_mails;
         $('#table-search').DataTable({
            paging: true,   
            searching: true,
            "bLengthChange": false,
            "bInfo" : false,
            "iDisplayLength": 10,
            "scrollX": false
         
         });

         body_loading_results.style.display = 'none';
         body_table_results.style.display = 'block';

      }else {
         body_loading_results.style.display = 'none';
         body_msg_results.style.display = 'block';
      }
   }else {

      body_loading_results.style.display = 'none';
      body_msg_results.style.display = 'none';
      body_table_results.style.display = 'none';
      body_search.style.display = 'block';

      body_table_results.innerHTML = '';
   }
}



$("#modal_search").on("hidden.bs.modal", function () {
   document.getElementById('body-search').style.display = 'block';
   document.getElementById('body-loading-results').style.display = 'none';
   document.getElementById('body-msg-results').style.display = 'none';
   document.getElementById('body-table-results').style.display = 'none';
   document.getElementById('body-table-results').innerHTML = '';
   document.getElementById('input_search_modal').value = '';
});


$("#input_search_modal").on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
      let { currentTarget } = e;
      search_messages(currentTarget);
    }
});

$("#input-search").on('keyup', function (e) {
   if (e.key === 'Enter' || e.keyCode === 13) {
     $('#modal_search').modal('show');
   }
});
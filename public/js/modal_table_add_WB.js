$('#msg_notification_modal').on('shown.bs.modal', async function (event) {



   let inputs = document.querySelectorAll("input[type='hidden']");
   let checkboxs = document.querySelectorAll("input[type='checkbox']");
   let checkboxs_emails = [];

   checkboxs.forEach( checkbox => {
      let checkbox_name = checkbox.name.split("-");
      let [ check_name, id_check ] = checkbox_name;
      if( check_name === 'check' && checkbox.checked ){
         checkboxs_emails.push( id_check );
      }
   });

 
   if( checkboxs_emails.length > 0){
      $( "#bw_accept_action" ).prop( "disabled", false );

      $('#modal_message_bw_id').hide();

      $('#modal_add_bw_id').show();
      $('#modal_option_bw_id').show();
      $('#modal_table_bw_id').show()

      let token = false;
      inputs.forEach( input => {
         if( input.name === 'token-modal' ) token = input.value;
      });

      let data = await get_data_bw( checkboxs_emails, token );
      let { error } = data;
      if( !error ){
         let { list_mails } = data;
         set_data_table_modal( list_mails );
      }
   } else{
      $('#modal_message_bw_id').show();

      $('#modal_add_bw_id').hide();
      $('#modal_option_bw_id').hide();
      $('#modal_table_bw_id').hide()

      $('#bw_accept_action').prop( "disabled", true );


      $('#table-body-warriors-rep_id').html('');
   }

 });
 $('#modal_message_bw_id').hide();


 const set_data_table_modal = ( list_mails )=>{
   $('#table-body-warriors-rep_id').html('');
   list_mails.forEach( row => {
      var row_rep = `<tr>

         <td class="py-1 px-2">
            <i class="fas fa-check"></i>
            <input type="hidden" name="check-${ row[0] }" value="ID-${ row[0] }" >
         </td>
         <td>${row[1]}</td>
         <td>${row[2]}</td>
         <td>${row[3]}</td>
         <td>${row[4]}</td>
      
      </tr>`;
      $('#table-body-warriors-rep_id').append(row_rep);

   });
 }


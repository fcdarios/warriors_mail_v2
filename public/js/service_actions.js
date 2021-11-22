
const service_actions = async ( action, token ) => {

   const data = await get_data_services( action, token );
   const { contenido } = data;

   console.log( data );

   set_data_service( 'body_service_reload', contenido ); 
   change_loading( 'card_service' );
}

const set_data_service = ( id_body, contenido ) =>{
   let card = document.getElementById( id_body );
   let body = `<pre>${ contenido }</pre>`;
   
   card.innerHTML = body;
}

const get_status_mta = async ( token ) => {
   let div_body_mta = document.getElementById( 'body_service_mta' );

   const data = await get_data_services('status_mta', token);
   const { status, labels } = data;
   const { reload, status_, start, stop, running, stopped } = labels;
   console.log(data)

   let btn_start_stop = '';
   if ( status === 1) {
      btn_start_stop = `
         <form action="services_action.php" method="POST">
            <input type="hidden" name="token" value="${ token }">
            <input type="hidden" name="action" value="stop">
            <button class="btn btn_services btn_services_stop mb-1">${ stop }</button>
         </form>
      `;
   }else {
      btn_start_stop = `
         <form action="services_action.php" method="POST">
            <input type="hidden" name="token" value="${ token }">
            <input type="hidden" name="action" value="start">
            <button class="btn btn_services btn_services_start mb-1">${ start }</button>
         </form>
      `;
   }
   
   let content_body = `
      <div class="">
         <form action="services_action.php" method="POST">
               <input type="hidden" name="token" value="${ token }">
               <input type="hidden" name="action" value="reload">
               <button class="btn btn_services btn_services_reload mb-1">${ reload }</button>
         </form>
      </div>
      <div class="">
         <form action="services_action.php" method="POST">
               <input type="hidden" name="token" value="${ token }">
               <input type="hidden" name="action" value="status">
               <button class="btn btn_services btn_services_status mb-1">${ status_ }</button>
         </form>
      </div>
      <div class="">
      ${ btn_start_stop }
      </div>
   `;

   document.getElementById('loading_service_mta').style.display = 'none';
   div_body_mta.innerHTML = content_body;
   div_body_mta.className = 'd-flex flex-wrap justify-content-between';
   div_body_mta.style.display = 'block';

   let mta_label = document.getElementById('mta_label_status');
   if ( status === 1 ) {
      mta_label.innerText = running;
      mta_label.style.backgroundColor = '#3EB595';
   }else {
      mta_label.innerText = stopped;
      mta_label.style.backgroundColor = '#B75050';
   }
}


const change_loading = ( id_card ) =>{
   document.getElementById('loading_service').style.display = 'none';
   document.getElementById( id_card ).style.display = 'block';
}
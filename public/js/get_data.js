
const get_data_search = async ( search_query = '', token ) => {
    
  let data = [];

  await $.ajax({
      type: 'POST',
      url: 'search_messages.php',
      data: { search_query, token },
      success: function ( respuesta ) {
        data = JSON.parse( respuesta );
      },
      error: function ( error ) {
          console.log(error);
      }
  });
  
  return data;
}

const get_data_ram = async (id_chart) => {

  const elegir_funcion = {
    data_function: "get_data_ram",
  };

  let data = await get_data_charts(elegir_funcion);
  data = JSON.parse(data);

  chart_RAM(id_chart, data);
};

const get_data_cpu = async (id_chart) => {

  const elegir_funcion = {
    data_function: "get_data_cpu",
  };

  let data = await get_data_charts(elegir_funcion);
  data = JSON.parse(data);
  chart_CPU(id_chart, data);
};


const get_data_bw = async ( ids, token ) => {
    
   let data = [];

   await $.ajax({
       type: 'POST',
       url: 'do_message_actions_bw_api.php',
       data: { ids, token },
       success: function ( respuesta ) {
         data = JSON.parse( respuesta );
       },
       error: function ( error ) {
           console.log(error);
       }
   });
   
   return data;
}


const get_data_charts = async (nombre_funcion) => {

   let data = [];
 
   await $.ajax({
     type: 'POST',
     url: 'dashboard_functions.php',
     data: nombre_funcion,
     success: function (respuesta) {
       data = respuesta;
 
     },
     error: function (error) {
       console.log(error)
     }
   });
 
   return data;
 }


 const get_data_detail = async ( querydetail, idMessage, list_url='' ) => {
    
   let data = [];

   await $.ajax({
       type: 'POST',
       url: 'detail_functions.php',
       data: { action: querydetail, id:idMessage, list_url:list_url },
       success: function (respuesta) {
           data = respuesta;
       },
       error: function ( error ) {
           console.log(error);
       }
   });
   
   return data;
}

const get_data_services = async ( action, token='' ) => {
    
  let data = [];

  await $.ajax({
      type: 'POST',
      url: 'services_action_api.php',
      data: { action, token },
      success: function (respuesta) {
        data = JSON.parse( respuesta );
      },
      error: function ( error ) {
          console.log(error);
      }
  });
  
  return data;
}
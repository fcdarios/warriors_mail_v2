const async_charts = async () =>  {

  const div_space_height = document.getElementById("card_diskspace").clientHeight;
  document.getElementById("card_trafficgraph").style.height = `${div_space_height}px`;

  console.log("UNO")
  await Promise.all( 
    [
      get_data_ram('chart_ram'),
      get_data_cpu('chart_cpu'),
      
    ] 
  );
    
  await chart_mails_last_week("graph_mails_last_week"),
  await chart_top_mail_relays("print_graph_rep_top_mail_relays"),
  await chart_top_viruses("print_graph_rep_top_viruses"),
  await chart_top_senders_by_volumen("print_graph_rep_top_senders_by_volumen")
  await chart_top_senders_by_quantity("print_graph_rep_top_senders_by_quantity"),
  await chart_top_recipients_by_quantity("print_graph_rep_top_recipients_by_quantity"),
  await chart_top_recipients_by_volumen("print_graph_rep_top_recipients_by_volumen"),
  await chart_top_senders_domain_by_quantity("print_graph_rep_top_senders_domain_by_quantity"),
  await chart_top_senders_domain_by_volumen("print_graph_rep_top_senders_domain_by_volumen"),
  await chart_top_recipients_domain_by_quantity("print_graph_rep_top_recipients_domain_by_quantity"),
  await chart_top_recipients_domain_by_volumen("print_graph_rep_top_recipients_domain_by_volumen"),


  console.log("...")
};


const charts_error = (data, id_chart) => {
  const { error, msg } = data;
  if (error) {
    document.getElementById('load' + id_chart).style.display = "none";

    let body = document.getElementById('body' + id_chart);
    body.innerText = msg;
    return true;
  }
  return false;
}

const chart_mails_last_week = async (id_chart) => {

  const nombre_funcion = {
    "data_function": "get_mails_last_week"
  };

  let data = await get_data_charts(nombre_funcion);
  data = JSON.parse(data);

  const { error, msg } = data;
  if (error) {
    document.getElementById('load_' + id_chart).style.display = "none";
    let body = document.getElementById(id_chart + '_body');
    body.innerHTML = `<p class="mt-5">${ msg }</p>`;
  }
  else
    chart_line(id_chart, data);
}

const chart_top_mail_relays = async (idChart) => {
  const nombre_funcion = {
    "data_function": "get_top_mail_relays"
  };

  let data = await get_data_charts(nombre_funcion);
  data = JSON.parse(data);

  if(charts_error(data, idChart) === false)
    chart_pie(idChart, data['count'], data['countconv'], data['hostname'], "");
}

const chart_top_viruses = async (idChart) => {
  const nombre_funcion = {
    "data_function": "get_top_viruses"
  };
  let data = await get_data_charts(nombre_funcion);
  data = JSON.parse(data);

  if(charts_error(data, idChart) === false)
    chart_pie(idChart, data['viruscount'], data['viruscount'], data['virusname'], "");
}

const chart_top_senders_by_volumen = async (idChart) => {
  const nombre_funcion = {
    "data_function": "get_top_senders_by_volumen"
  };

  let data = await get_data_charts(nombre_funcion);
  data = JSON.parse(data);

  if(charts_error(data, idChart) === false)
    chart_pie(idChart, data['size'], data['sizeconv'], data['name'], "");
}

const chart_top_senders_by_quantity = async (idChart) => {
  const nombre_funcion = {
    "data_function": "get_top_senders_by_quantity"
  };

  let data = await get_data_charts(nombre_funcion);
  data = JSON.parse(data);

  if(charts_error(data, idChart) === false)
    chart_pie(idChart, data['count'], data['countconv'], data['name'], "");
}

const chart_top_recipients_by_quantity = async (idChart) => {
  const nombre_funcion = {
    "data_function": "get_top_recipients_by_quantity"
  };

  let data = await get_data_charts(nombre_funcion);

  data = JSON.parse(data);

  if(charts_error(data, idChart) === false)
    chart_pie(idChart, data['count'], data['countconv'], data['name'], "");
}

const chart_top_recipients_by_volumen = async (idChart) => {
  const nombre_funcion = {
    "data_function": "get_top_recipients_by_volumen"
  };

  let data = await get_data_charts(nombre_funcion);
  data = JSON.parse(data);

  if(charts_error(data, idChart) === false)
    chart_pie(idChart, data['size'], data['sizeconv'], data['name'], "");
}

const chart_top_senders_domain_by_quantity = async (idChart) => {
  const nombre_funcion = {
    "data_function": "get_top_senders_domain_by_quantity"
  };

  let data = await get_data_charts(nombre_funcion);
  data = JSON.parse(data);

  if(charts_error(data, idChart) === false)
    chart_pie(idChart, data['count'], data['countconv'], data['name'], "");
}

const chart_top_senders_domain_by_volumen = async (idChart) => {
  const nombre_funcion = {
    "data_function": "get_top_senders_domain_by_volumen"
  };

  let data = await get_data_charts(nombre_funcion);
  data = JSON.parse(data);

  if(charts_error(data, idChart) === false)
    chart_pie(idChart, data['size'], data['sizeconv'], data['name'], "");
}

const chart_top_recipients_domain_by_volumen = async (idChart) => {
  const nombre_funcion = {
    "data_function": "get_top_recipients_domain_by_volumen"
  };

  let data = await get_data_charts(nombre_funcion);
  data = JSON.parse(data);

  if(charts_error(data, idChart) === false)
    chart_pie(idChart, data['size'], data['sizeconv'], data['name'], "");
}

const chart_top_recipients_domain_by_quantity = async (idChart) => {
  const nombre_funcion = {
    "data_function": "get_top_recipients_domain_by_quantity"
  };

  let data = await get_data_charts(nombre_funcion);
  data = JSON.parse(data);

  if(charts_error(data, idChart) === false)
    chart_pie(idChart, data['count'], data['countconv'], data['name'], "");
}

const pieBackgroundColors = [

  '#a52424', '#9a224f', '#7c356c', '#554375', '#35496c',
  '#2f4858', '#2b3a3a', '#326158', '#47896a', '#3e535b'

];

function chart_pie(id_chart, datapie, formattedData, labels, title) {

  var ctx = document.getElementById(id_chart).getContext('2d');;

  let mypiechart = new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: labels,
      datasets: [{
        label: title,
        data: datapie,
        borderColor: getChartBgBorderColors(datapie.length),
        backgroundColor: getChartBgColors(datapie.length),
      }]
    },
    options: {
      title: {
        display: false,
      },
      legend: {
        position: "left",
        display: false,
        labels: {
          fontColor: "#000",
          generateLabels: function (graph) {
            var defaultLabels = Chart.defaults.doughnut.legend.labels.generateLabels(graph);
            return defaultLabels;
          }
        }
      },
      tooltips: {
        callbacks: {
          label: function (tooltipItem, data) {

            var dataset = data.datasets[tooltipItem.datasetIndex];
            var tooltipLabel = data.labels[tooltipItem.index];
            var itemData = dataset.data[tooltipItem.index];
            var total = 0;
            for (var i in dataset.data) {
              total += Number(dataset.data[i]);
            }

            var tooltipPercentage = Math.round((itemData / total) * 100);

            //COLON specified on main page via php __('colon99')
            var tooltipOutput = " " + tooltipLabel + COLON + " " + formattedData[tooltipItem.index];

            if (tooltipPercentage < 3) {
              tooltipOutput += " (" + tooltipPercentage + "%)";
            }
            return tooltipOutput;
          }
        }
      },

      animation: {
        onProgress: drawPersistentPercentValues,
        onComplete: drawPersistentPercentValues
      },
      aspectRatio: 1,
      responsive: true,
      hover: { animationDuration: 0 },
      plugins: {
        datalabels: {
          color: '#000',
          textAlign: 'center',
          font: {
            lineHeight: 1.6
          },
          display: true,
          formatter: function (value, ctx) {
            return ctx.chart.data.labels[ctx.dataIndex] + '\n' + value + '%';
          }
        },

      }
    },
  });

  document.getElementById(id_chart).style.display = "block";
  var load = 'load';
  document.getElementById('load' + id_chart).style.display = "none";
}

function drawPersistentPercentValues() {
  var ctx = this.chart.ctx;

  ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, "normal", Chart.defaults.global.defaultFontFamily);
  ctx.fillStyle = "#eee";

  this.data.datasets.forEach(function (dataset) {
    var sum = 0;
    for (var i = 0; i < dataset.data.length; i++) {
      // if(dataset.hidden === true || dataset._meta[0].data[i].hidden === true){ continue; }
      sum += Number(dataset.data[i]);
    }
    var curr = 0;
    for (var i = 0; i < dataset.data.length; i++) {
      //if(dataset.hidden === true || dataset._meta[0].data[i].hidden === true){ continue; }
      var part = dataset.data[i] / sum;
      if (dataset.data[i] !== null && part * 100 > 2) {
        var model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model;
        var radius = model.outerRadius - 10; //where to place the text around the center
        var x = Math.sin((curr + part / 2) * 2 * Math.PI) * radius;
        var y = Math.cos((curr + part / 2) * 2 * Math.PI) * radius;
        ctx.fillText((part < 0.1 ? " " : "") + (part * 100).toFixed(0) + "%", model.x + x * 0.95 - 15, model.y - y * 0.96 - 8);

        curr += part;
      }
    }
  });

}

function getChartBgColors(count) {
  var bgColors = [];
  for (var i = 0; bgColors.length < count; i++) {
    bgColors.push(pieBackgroundColors[i] + 'AA');
  }
  return bgColors;
}

function getChartBgBorderColors(count) {
  var bgColors = [];
  for (var i = 0; bgColors.length < count; i++) {
    bgColors.push(pieBackgroundColors[i]);
  }
  return bgColors;
}



/******modify the colors here****/
var volumeColor = '#92F56F'; // green
var mailColor = '#4973f7'; // blue
var virusColor = '#B22222'; // dark red
var spamColor = '#EE6262'; // red
var mcpColor = '#b9e3f9'; // light blue
/*******************************/

var defaultColors = [
  mailColor,
  virusColor,
  spamColor,
  volumeColor,
  mcpColor
];

const chart_line = (id_chart, data) => {

  

  const ctx = document.getElementById(id_chart).getContext('2d');
  const { dataset, labels, size, xaxis } = data;

  const myChart = new Chart(ctx, {
    type: "line",
    data: {
      labels: xaxis,
      datasets: (() => {
        dataset_temp = [];

        for (let i = 0; i < size; i++) {
          let label_name = labels[i];
          let data_t = dataset[i];
          let color = defaultColors[i];

          dataset_temp.push({
            label: label_name,
            data: data_t,
            backgroundColor: color + '99',
            borderColor: color + 'DD',
            borderWidth: 2,
            pointRadius: 2,
            pointBorderColor: color,
            pointBackgroundColor: color,
            type: 'line',
          });

        }
        return dataset_temp;
      })()
    },
    options: {
      maintainAspectRatio: false,
      responsive: true,

    },
  });
  document.getElementById(id_chart).style.display = "block";
  const load = 'load_';
  document.getElementById(load.concat(id_chart)).style.display = "none";
};



const chart_CPU = (id_chart, data) => {

  const colors = {
    text: '#000000AA'
  }
  const chartColors_info = {
    used_user: '#bc2a2a',
    used_system: '#0D7061',
    available: '#888888'
  };

  const {
    labels,
    CPU_used_total,
    CPU_used_user,
    CPU_used_system,
    CPU_available
  } = data;

  const [
    total,
    used_total,
    used_user,
    used_system,
    available
  ] = labels;


  let div = document.getElementById(id_chart + '_text');
  div.innerText = 'CPU';

  const myChart = new Chart(id_chart, {
    type: 'doughnut',
    data: {
      labels: [used_user, used_system, available],
      datasets: [
        {
          label: 'RAM',
          data: [CPU_used_user, CPU_used_system, CPU_available],
          backgroundColor: [chartColors_info.used_user + 'CC', chartColors_info.used_system + 'CC', chartColors_info.available + '90'],
          borderColor: [chartColors_info.used_user, chartColors_info.used_system, chartColors_info.available + '33'],
          borderWidth: 0.5
        }
      ]
    },
    options: {
      responsive: true,
      circumference: Math.PI,
      rotation: Math.PI,
      cutoutPercentage: 60,
      title: {
        display: true,
        fontStyle: 'bold',
        fontSize: 12,
        fontColor: colors.text,
        text: CPU_used_total + '%',
        padding: 0,
        position: 'bottom',
        verticalAlign: 'start',
        align: 'center',
        y: 0,
      },
      legend: {
        display: false,
      },
      tooltips: {
        callbacks: {
          label: function (tooltipItem, data) {

            const dataset = data.datasets[tooltipItem.datasetIndex];

            const currentValue = dataset.data[tooltipItem.index];
            let label = '';

            if (currentValue === CPU_used_user) {
              label = used_user + ': ' + currentValue + '%';
            } else if (currentValue === CPU_used_system) {
              label = used_system + ': ' + currentValue + '%';
            } else if (currentValue === CPU_available) {
              label = available + ': ' + currentValue + '%';
            }

            return label;
          }
        }
      }
    }
  });

}

const chart_RAM = (id_chart, data) => {
  const colors = {
    text: '#000000AA'
  }
  const chartColors = {
    used: '#bc2a2a',
    available: '#888888'
  };

  const {
    labels,
    RAM_total,
    RAM_used,
    RAM_available,
    RAM_total_Percent,
    RAM_used_Percent,
    RAM_available_Percent
  } = data;

  const [, used, available] = labels;

  let div = document.getElementById(id_chart + '_text');
  div.innerText = 'Ram: ' + RAM_total;

  const myChart = new Chart(id_chart, {
    type: 'doughnut',
    data: {
      labels: [used, available],
      datasets: [{
        label: 'RAM',
        data: [RAM_used_Percent, RAM_available_Percent],
        backgroundColor: [chartColors.used + 'CC', chartColors.available + '90'],
        borderColor: [chartColors.used, chartColors.available + '33'],
        borderWidth: 0.5
      }]
    },
    options: {
      responsive: true,
      circumference: Math.PI,
      rotation: Math.PI,
      cutoutPercentage: 60,
      title: {
        display: true,
        fontStyle: 'bold',
        fontSize: 12,
        fontColor: colors.text,
        text: RAM_used_Percent + '%',
        padding: 0,
        position: 'bottom',
        verticalAlign: 'start',
        align: 'center',
        y: 0,
      },
      legend: {
        display: false,
      },
      tooltips: {
        callbacks: {
          label: function (tooltipItem, data) {

            const dataset = data.datasets[tooltipItem.datasetIndex];

            const total = dataset.data.reduce(function (previousValue, currentValue, currentIndex, array) {
              return previousValue + currentValue;
            });

            const currentValue = dataset.data[tooltipItem.index];

            let percentage = Math.floor(((currentValue / total) * 100) + 0.5);

            if (percentage > RAM_used_Percent - 1 && percentage < RAM_used_Percent + 1)
              percentage = 'Used: ' + RAM_used_Percent;
            else
              percentage = 'Available: ' + RAM_available_Percent;

            return ' ' + percentage + "%";
          }
        }
      }
    }
  });

  let td_total = document.getElementById(id_chart + '_total_text');
  let td_used = document.getElementById(id_chart + '_used_text');
  let td_available = document.getElementById(id_chart + '_available_text');
  td_total.innerText = RAM_total;
  td_used.innerText = RAM_used;
  td_available.innerText = RAM_available;
}


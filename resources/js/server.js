// Get Server info
$('#mainloading').removeClass('hidden');

// Crontab editor
var crontab = ace.edit("crontab");
crontab.setTheme("ace/theme/monokai");
crontab.session.setMode("ace/mode/sh");

// Crontab edit
$('#editcrontab').click(function () {
    $('#crontabModal').modal();
});

//Get Data for Other
function getDataNoDT(url) {
    $.ajax({
        type: 'GET',
        url: url,
        success: function(data) {
            localStorage.otherdata = '';
            localStorage.otherdata = JSON.stringify(data);
        }
    });
}

//IP Validation
function ipValidate(ip) {
    return (/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(ip))
}

// Crontab Submit
$('#crontabsubmit').click(function () {
    $.ajax({
        url: '/api/servers/' + server_id,
        type: 'PATCH',
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify({
            'cron': crontab.getSession().getValue(),
        }),
        beforeSend: function () {
            $('#crontableloading').removeClass('hidden');
        },
        success: function (data) {
            $('#crontableloading').addClass('hidden');
            $('#crontabModal').modal('toggle');
            serverInit();
        },
    });
});

// Server Init
function serverInit() {
    getDataNoDT('/api/servers', false);
    $.ajax({
        url: '/api/servers/' + server_id,
        type: 'GET',
        success: function (data) {
            $('#mainloading').addClass('hidden');
            $('#serveriptop').html(data.ip);
            $('#serversites').html(data.sites);
            $('#maintitle').html('- ' + data.name);
            $('#servername').val(data.name);
            $('#serverip').val(data.ip);
            $('#serverprovider').val(data.provider);
            $('#serverlocation').val(data.location);
            $('#currentip').val(data.ip);
            crontab.session.setValue(data.cron);
            $('#serverbuild').empty();
            if (data.build) {
                $('#serverbuild').html(data.build);
            } else {
                $('#serverbuild').html(trans_server.unknown);
            }
            switch (data.php) {
                case '8.3':
                    $('#php83').attr("selected", "selected");
                    break;
                case '8.2':
                    $('#php82').attr("selected", "selected");
                    break;
                case '8.1':
                    $('#php81').attr("selected", "selected");
                    break;
                case '8.0':
                    $('#php80').attr("selected", "selected");
                    break;
                case '7.4':
                    $('#php74').attr("selected", "selected");
                    break;
                case '7.3':
                    // Append legacy php 7.3
                    $('#phpver').append('<option value="7.3" selected>7.3</option>');
                    break;
                default:
                    break;
            }
        },
    });
}

// Init variables
serverInit();

// Ping
function getPing() {
    $.ajax({
        url: '/api/servers/' + server_id + '/ping',
        type: 'GET',
        beforeSend: function () {
            $('#serverping').empty();
            $('#serverping').html('<i class="fa fa-circle-notch fa-spin fa-spinner" title="' + trans_server.loading_data + '"></i>');
        },
        success: function (data) {
            $('#serverping').empty();
            $('#serverping').html('<i class="fa fa-check text-success"></i>');
        },
    });
}

setInterval(function () {
    getPing();
}, 10000);
getPing();

// Change PHP
$('#changephp').click(function () {
    $.ajax({
        url: '/api/servers/' + server_id,
        type: 'PATCH',
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify({
            'php': $('#phpver').val(),
        }),
        beforeSend: function () {
            $('#changephp').html('<i class="fas fa-circle-notch fa-spin" title="' + trans_server.loading_please_wait + '"></i>');
        },
        success: function (data) {
            $('#changephp').empty();
            $('#changephp').html('<i class="fas fas fa-edit"></i>');
        },
    });
    serverInit();
});

// Restart nginx
$('#restartnginx').click(function () {
    $.ajax({
        url: '/api/servers/' + server_id + '/servicerestart/nginx',
        type: 'POST',
        beforeSend: function () {
            $('#loadingnginx').removeClass('hidden');
        },
        success: function (data) {
            $('#loadingnginx').addClass('hidden');
        },
    });
});

// Restart php
$('#restartphp').click(function () {
    $.ajax({
        url: '/api/servers/' + server_id + '/servicerestart/php',
        type: 'POST',
        beforeSend: function () {
            $('#loadingphp').removeClass('hidden');
        },
        success: function (data) {
            $('#loadingphp').addClass('hidden');
        },
    });
});

// Restart mysql
$('#restartmysql').click(function () {
    $.ajax({
        url: '/api/servers/' + server_id + '/servicerestart/mysql',
        type: 'POST',
        beforeSend: function () {
            $('#loadingmysql').removeClass('hidden');
        },
        success: function (data) {
            $('#loadingmysql').addClass('hidden');
        },
    });
});

// Restart redis
$('#restartredis').click(function () {
    $.ajax({
        url: '/api/servers/' + server_id + '/servicerestart/redis',
        type: 'POST',
        beforeSend: function () {
            $('#loadingredis').removeClass('hidden');
        },
        success: function (data) {
            $('#loadingredis').addClass('hidden');
        },
    });
});

// Restart supervisor
$('#restartsupervisor').click(function () {
    $.ajax({
        url: '/api/servers/' + server_id + '/servicerestart/supervisor',
        type: 'POST',
        beforeSend: function () {
            $('#loadingsupervisor').removeClass('hidden');
        },
        success: function (data) {
            $('#loadingsupervisor').addClass('hidden');
        },
    });
});

// Root Reset
$('#rootreset').click(function () {
    $('#rootresetModal').modal();
});

// Root Reset Submit
$('#rootresetsubmit').click(function () {
    $('#rootresetloading').removeClass('hidden');
    $.ajax({
        url: '/api/servers/' + server_id + '/rootreset',
        type: 'POST',
        success: function (data) {
            success(trans_server.new_password_success + ':<br><b>' + data.password + '</b>'
            )
            ;
            $(window).scrollTop(0);
            $('#rootresetModal').modal('toggle');
        },
        complete: function () {
            $('#rootresetloading').addClass('hidden');
        }
    });
});

//Check IP conflict (edit)
function ipConflictEdit(ip, server_id) {
    conflict = 0;
    JSON.parse(localStorage.otherdata).forEach(server => {
        if (ip === server.ip && server.server_id !== server_id) {
            conflict = conflict + 1;
        }
    });
    return conflict;
}

// Update Server
$('#updateServer').click(function () {
    $('#ipnotice').addClass('hidden');
    if ($('#serverip').val() != $('#currentip').val()) {
        $('#newip').html($('#serverip').val());
        $('#ipnotice').removeClass('hidden');
    }
    validation = true;
    if (!$('#servername').val() || $('#servername').val().length < 3) {
        $('#servername').addClass('is-invalid');
        $('#submit').addClass('disabled');
        validation = false;
    }
    if (!$('#serverip').val() || !ipValidate($('#serverip').val()) || ipConflictEdit($('#serverip').val(), server_id) > 0) {
        $('#serverip').addClass('is-invalid');
        $('#submit').addClass('disabled');
        validation = false;
    }
    if (validation) {
        $('#loading').addClass('hidden');
        $('#updateServerModal').modal();
    }
});

// Update Server Validation
$('#servername').keyup(function () {
    $('#servername').removeClass('is-invalid');
    $('#submit').removeClass('disabled');
});
$('#serverip').keyup(function () {
    $('#serverip').removeClass('is-invalid');
    $('#submit').removeClass('disabled');
});

// Update Server Submit
$('#submit').click(function () {
    $.ajax({
        url: '/api/servers/' + server_id,
        type: 'PATCH',
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify({
            'name': $('#servername').val(),
            'ip': $('#serverip').val(),
            'provider': $('#serverprovider').val(),
            'location': $('#serverlocation').val()
        }),
        beforeSend: function () {
            $('#loading').removeClass('hidden');
        },
        success: function (data) {
            serverInit();
            $('#loading').addClass('hidden');
        },
        complete: function () {
            $('#ipnotice').addClass('hidden');
            $('#updateServerModal').modal('toggle');
        }
    });
});

// Charts style
Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#292b2c';

// CPU Chart
cpu = document.getElementById("cpuChart");
cpuChart = new Chart(cpu, {
    type: 'line',
    showXLabels: 10,
    data: {
        labels: [],
        datasets: [{
            label: "CPU Load (%)",
            lineTension: 0.3,
            backgroundColor: "rgba(2,117,216,0.2)",
            borderColor: "rgba(2,117,216,1)",
            pointRadius: 5,
            pointBackgroundColor: "rgba(2,117,216,1)",
            pointBorderColor: "rgba(255,255,255,0.8)",
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(2,117,216,1)",
            pointHitRadius: 50,
            pointBorderWidth: 2,
            data: []
        }],
    },
    options: {
        scales: {
            xAxes: [{
                time: {
                    unit: 'date'
                },
                gridLines: {
                    display: false
                },
                ticks: {
                    maxTicksLimit: 7
                }
            }],
            yAxes: [{
                ticks: {
                    min: 0,
                    max: 100,
                    maxTicksLimit: 5
                },
                gridLines: {
                    color: "rgba(0, 0, 0, .125)",
                }
            }]
        },
        legend: {
            display: false
        }
    }
});

// RAM Chart
ram = document.getElementById("ramChart");
ramChart = new Chart(ram, {
    type: 'line',
    showXLabels: 10,
    data: {
        labels: [],
        datasets: [{
            label: "RAM Usage (%)",
            lineTension: 0.3,
            backgroundColor: "rgba(2,117,216,0.2)",
            borderColor: "rgba(2,117,216,1)",
            pointRadius: 5,
            pointBackgroundColor: "rgba(2,117,216,1)",
            pointBorderColor: "rgba(255,255,255,0.8)",
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(2,117,216,1)",
            pointHitRadius: 50,
            pointBorderWidth: 2,
            data: []
        }],
    },
    options: {
        scales: {
            xAxes: [{
                time: {
                    unit: 'date'
                },
                gridLines: {
                    display: false
                },
                ticks: {
                    maxTicksLimit: 7
                }
            }],
            yAxes: [{
                ticks: {
                    min: 0,
                    max: 100,
                    maxTicksLimit: 5
                },
                gridLines: {
                    color: "rgba(0, 0, 0, .125)",
                }
            }]
        },
        legend: {
            display: false
        }
    }
});

//CPU & RAM charts
function chartsUpdate(cpuChart, ramChart) {
    $.ajax({
        type: 'GET',
        url: '/api/servers/' + server_id + '/healthy',
        success: function (result) {
            //HD
            $('#hd').empty();
            $('#hd').removeClass('btn-secondary');
            $('#hd').removeClass('btn-success');
            $('#hd').removeClass('btn-warning');
            $('#hd').removeClass('btn-danger');
            $('#hd').html(result.hdd + '%');
            if (result.hdd < 61) {
                $('#hd').addClass('btn-success');
            }
            if (result.hdd > 60) {
                $('#hd').addClass('btn-warning');
            }
            if (result.hdd > 85) {
                $('#hd').addClass('btn-danger');
            }
            //CPU
            labels = cpuChart.data.labels
            data = cpuChart.data.datasets[0].data
            if (labels.length > 10) {
                labels = labels.shift();
            } else {
                var d = new Date();
                if (d.getHours() < 10) {
                    hours = '0' + d.getHours();
                } else {
                    hours = d.getHours();
                }
                if (d.getMinutes() < 10) {
                    minutes = '0' + d.getMinutes();
                } else {
                    minutes = d.getMinutes();
                }
                labels.push(hours + ':' + minutes);
            }
            if (data.length > 10) {
                data = data.shift();
            } else {
                data.push(result.cpu);
            }
            cpuChart.update();
            //RAM
            labels = ramChart.data.labels
            data = ramChart.data.datasets[0].data
            if (labels.length > 10) {
                labels = labels.shift();
            } else {
                var d = new Date();
                if (d.getHours() < 10) {
                    hours = '0' + d.getHours();
                } else {
                    hours = d.getHours();
                }
                if (d.getMinutes() < 10) {
                    minutes = '0' + d.getMinutes();
                } else {
                    minutes = d.getMinutes();
                }
                labels.push(hours + ':' + minutes);
            }
            if (data.length > 10) {
                data = data.shift();
            } else {
                data.push(result.ram);
            }
            ramChart.update();
        }
    })
}

//First step charts
setTimeout(function (cpuChart, ramChart) {
    chartsUpdate(cpuChart, ramChart);
}, 500, cpuChart, ramChart);

//Other steps charts
setInterval(function (cpuChart, ramChart) {
    chartsUpdate(cpuChart, ramChart);
}, 30000, cpuChart, ramChart);

//Init charts
chartsUpdate(cpuChart, ramChart);

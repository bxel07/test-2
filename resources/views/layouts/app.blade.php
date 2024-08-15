<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />

        <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    
        <link href="https://demos.creative-tim.com/soft-ui-dashboard-tailwind/assets/css/nucleo-icons.css" rel="stylesheet" />    
        <script src="https://unpkg.com/@popperjs/core@2"></script>

        <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css"  rel="stylesheet" />


        <!-- Scripts -->
        @vite(['resources/css/app.css','resources/css/loopple.css', 'resources/css/theme.css','resources/js/app.js', 'resources/js/loopple.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <main>
                @if(auth()->user()->role === 'admin')
                    <x-partials.dashboard.aside></x-partials.dashboard.aside>
                @else
                    <x-partials.dashboard.aside-user></x-partials.dashboard.aside-user>
                @endif
                <div class="ease-soft-in-out xl:ml-68.5 relative h-full max-h-screen rounded-xl transition-all duration-200" id="panel">
                    <nav class="relative flex flex-wrap items-center justify-between px-0 py-2 mx-6 transition-all shadow-none duration-250 ease-soft-in rounded-2xl lg:flex-nowrap lg:justify-start" id="navbarTop" navbar-scroll="true">
                        <div class="flex items-center justify-between w-full px-4 py-1 mx-auto flex-wrap-inherit">
                            <div class="flex items-center mt-2 grow sm:mt-0 sm:mr-6 md:mr-0 lg:flex lg:basis-auto">
                                <div class="flex items-center md:ml-auto md:pr-4">
                                </div>
                                <ul class="flex flex-row justify-end pl-0 mb-0 list-none md-max:w-full">
                                    <li class="flex items-center pl-4 xl:hidden">
                                        <a href="javascript:;" class="block p-0 text-sm transition-all ease-nav-brand text-slate-500" sidenav-trigger="">
                                            <div class="w-4.5 overflow-hidden">
                                                <i class="ease-soft mb-0.75 relative block h-0.5 rounded-sm bg-slate-500 transition-all"></i>
                                                <i class="ease-soft mb-0.75 relative block h-0.5 rounded-sm bg-slate-500 transition-all"></i>
                                                <i class="ease-soft relative block h-0.5 rounded-sm bg-slate-500 transition-all"></i>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>

                    <div class="w-full px-6 py-6 mx-auto loopple-min-height-78vh text-slate-500">
                        {{ $slot }}
                    </div>

                </div>
            </main>
        </div>
        <script src="https://demos.creative-tim.com/soft-ui-dashboard-tailwind/assets/js/plugins/chartjs.min.js"></script>

        <script src="https://demos.creative-tim.com/soft-ui-dashboard-tailwind/assets/js/plugins/perfect-scrollbar.min.js" async></script>
    
        <script async defer src="https://buttons.github.io/buttons.js"></script>
    
        <script src="https://cdn.jsdelivr.net/gh/Loopple/loopple-public-assets@main/soft-ui-dashboard-tailwind/js/soft-ui-dashboard-tailwind.js" async></script>
        <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>

        
        <script>
            if (document.querySelector(".chart-bars")) {
        
                var chartsBars = document.querySelectorAll(".chart-bars");
            
                chartsBars.forEach(function(chart) {
                    new Chart(chart, {
                        type: "bar",
                        data: {
                            labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                            datasets: [{
                                label: "Sales",
                                tension: 0.4,
                                borderWidth: 0,
                                borderRadius: 4,
                                borderSkipped: false,
                                backgroundColor: "#fff",
                                data: [450, 200, 100, 220, 500, 100, 400, 230, 500],
                                maxBarThickness: 6,
                            }, ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false,
                                },
                            },
                            interaction: {
                                intersect: false,
                                mode: "index",
                            },
                            scales: {
                                y: {
                                    grid: {
                                        drawBorder: false,
                                        display: false,
                                        drawOnChartArea: false,
                                        drawTicks: false,
                                    },
                                    ticks: {
                                        suggestedMin: 0,
                                        suggestedMax: 600,
                                        beginAtZero: true,
                                        padding: 15,
                                        font: {
                                            size: 14,
                                            family: "Open Sans",
                                            style: "normal",
                                            lineHeight: 2,
                                        },
                                        color: "#fff",
                                    },
                                },
                                x: {
                                    grid: {
                                        drawBorder: false,
                                        display: false,
                                        drawOnChartArea: false,
                                        drawTicks: false,
                                    },
                                    ticks: {
                                        display: false,
                                    },
                                },
                            },
                        },
                    });
            
                });
            
            };
            
            if (document.querySelector(".chart-line")) {
                var chartsLine = document.querySelectorAll(".chart-line");
            
                chartsLine.forEach(function(chart) {
                    var ctx = chart.getContext("2d");
            
                    var gradientStroke1 = ctx.createLinearGradient(0, 230, 0, 50);
            
                    gradientStroke1.addColorStop(1, "rgba(203,12,159,0.2)");
                    gradientStroke1.addColorStop(0.2, "rgba(72,72,176,0.0)");
                    gradientStroke1.addColorStop(0, "rgba(203,12,159,0)"); //purple colors
            
                    var gradientStroke2 = ctx.createLinearGradient(0, 230, 0, 50);
            
                    gradientStroke2.addColorStop(1, "rgba(20,23,39,0.2)");
                    gradientStroke2.addColorStop(0.2, "rgba(72,72,176,0.0)");
                    gradientStroke2.addColorStop(0, "rgba(20,23,39,0)"); //purple colors
            
                    new Chart(ctx, {
                        type: "line",
                        data: {
                            labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                            datasets: [{
                                    label: "Mobile apps",
                                    tension: 0.4,
                                    borderWidth: 0,
                                    pointRadius: 0,
                                    borderColor: "#cb0c9f",
                                    borderWidth: 3,
                                    backgroundColor: gradientStroke1,
                                    fill: true,
                                    data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
                                    maxBarThickness: 6,
                                },
                                {
                                    label: "Websites",
                                    tension: 0.4,
                                    borderWidth: 0,
                                    pointRadius: 0,
                                    borderColor: "#3A416F",
                                    borderWidth: 3,
                                    backgroundColor: gradientStroke2,
                                    fill: true,
                                    data: [30, 90, 40, 140, 290, 290, 340, 230, 400],
                                    maxBarThickness: 6,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false,
                                },
                            },
                            interaction: {
                                intersect: false,
                                mode: "index",
                            },
                            scales: {
                                y: {
                                    grid: {
                                        drawBorder: false,
                                        display: true,
                                        drawOnChartArea: true,
                                        drawTicks: false,
                                        borderDash: [5, 5],
                                    },
                                    ticks: {
                                        display: true,
                                        padding: 10,
                                        color: "#b2b9bf",
                                        font: {
                                            size: 11,
                                            family: "Open Sans",
                                            style: "normal",
                                            lineHeight: 2,
                                        },
                                    },
                                },
                                x: {
                                    grid: {
                                        drawBorder: false,
                                        display: false,
                                        drawOnChartArea: false,
                                        drawTicks: false,
                                        borderDash: [5, 5],
                                    },
                                    ticks: {
                                        display: true,
                                        color: "#b2b9bf",
                                        padding: 20,
                                        font: {
                                            size: 11,
                                            family: "Open Sans",
                                            style: "normal",
                                            lineHeight: 2,
                                        },
                                    },
                                },
                            },
                        },
                    });
            
                });
            };
        </script>
    </body>
</html>

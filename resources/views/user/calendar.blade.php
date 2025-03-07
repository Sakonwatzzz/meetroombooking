<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Calendar</title>

    <!-- Bootstrap 5 (เลือกโหลดแค่เวอร์ชันเดียว) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/locales/th.js"></script>

    <!-- Tailwind CSS (เวอร์ชันใหม่) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js (เลือกโหลดแค่ตัวเดียว) -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
    <link rel="stylesheet" href="styles.css?v=2">

    <!-- DNS Prefetch (ไม่จำเป็นต้องใส่สำหรับ unpkg และ jsdelivr) -->

    <script>
        let currentUserId = @json(auth()->id());
    </script>
</head>

<style>
    .my-event-class {
        border-radius: 10px;
        font-weight: bold;
    }

    .modal-header {
        background: #f1f1f1;
        padding: 15px;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        justify-items: center;
        justify-content: center;
        align-content: center;
    }

    /* Modal styles */
    .modal {
        display: none;
        position: fixed;
        /* z-index: -1; */
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(5px);
        align-items: center;
        justify-items: center;
        justify-content: center;
        align-content: center;
    }

    .modal-content {
        background: linear-gradient(135deg, #ffffff, #f9f9f9);
        border-radius: 12px;
        box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.3);
        padding: 20px;
        width: 500px;
        max-width: 200%;
        /* ลดความกว้างลงให้ดูพอดี */
        /* ให้ responsive กับหน้าจอ */
        text-align: left;
        position: relative;
        animation: fadeIn 0.3s ease-in-out;
    }

    .modal-body {
        padding: 10px;
        flex-direction: column;
        gap: 5px;

        /* เพิ่มระยะห่างของข้อความ */
    }

    .modal-body p {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 1.1rem;
        padding: 8px;
        border-radius: 8px;
        color: #333;
        flex-wrap: wrap;
        /* ให้ข้อความขึ้นบรรทัดใหม่หากเกินขนาด */
        white-space: normal;
        /* ป้องกันข้อความยาวเกินไป */
        word-wrap: break-word;
        /* ให้ขึ้นบรรทัดใหม่หากข้อความยาว */
    }

    .modal-body strong {
        font-weight: bold;
        color: #000000;
    }

    .modal-body span {
        font-weight: 500;
        color: #222;
    }

    .close-btn {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        position: absolute;
        top: 10px;
        right: 20px;
    }

    .close-btn:hover {
        color: #ff5e00;
        cursor: pointer;
    }

    .close-btn:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    /* Content styling */
    h4 {
        font-size: 1.5rem;
        margin-bottom: 20px;
    }

    p {
        font-size: 1.1rem;
        margin: 5px 0;
    }

    strong {
        font-weight: bold;

    }

    .my-event-class {
        background: linear-gradient(135deg, #ff7eb3, #ff758c);
        border-radius: 10px;
        font-weight: bold;
        color: white;
        padding: 5px;
    }

    @keyframes fadeIn {
        from {
            transform: translateY(-30px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-footer {
        width: 50px;
        background-color: #ff4d4d;
        color: white;
        border-radius: 20px;
        padding: 8px 15px;
        font-size: 16px;
        cursor: pointer;
        display: inline-block;
        margin-top: 15px;
        transition: 0.3s;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .modal-body h4 {
        border-radius: 13px;
        color: #0d9c00;
        display: flex;
        font-size: 20px;
        justify-content: center;
        align-items: center;
        height: 25px;
        width: auto;
    }

    .modal-title {
        font-size: 30px;
    }

    @media (max-width: 768px) {
        .modal-content {
            width: 95%;
            max-width: 90%;
            padding: 15px;
        }

        .modal-title {
            font-size: 24px;
        }

        .modal-body p {
            font-size: 1rem;
            flex-direction: column;
            /* ให้ strong และ span อยู่คนละบรรทัด */
            text-align: left;
        }

        .modal-body strong {
            min-width: 100%;
            display: block;
        }
    }

    /* ปรับปุ่ม Toolbar */
    .fc-toolbar-chunk button {
        background-color: #28a745 !important;
        /* สีเขียว */
        color: white !important;
        border-radius: 15px !important;
        padding: 8px 12px !important;
        border: none !important;
        margin-right: 10px !important;
        /* เพิ่มระยะห่าง */
    }

    .fc-toolbar-chunk button:hover {
        background-color: #218838 !important;
    }

    /* ปรับแถบหัวปฏิทิน */
    .fc-toolbar {
        background-color: #f8f9fa !important;
        padding: 10px !important;
        border-bottom: 2px solid #ddd !important;
    }

    .fc-toolbar h2 {
        font-size: 20px !important;
        font-weight: bold;
        color: #333;
    }

    /* จัดขนาดปฏิทิน */
    #calendar {
        width: 50%;
        max-width: auto;
        margin: auto;
    }

    /* ปรับแต่งป้ายอีเวนต์ให้เป็นโค้งมน */
    .fc-event {
        border-radius: 12px !important;
        padding: 6px 10px !important;
        font-weight: bold;
        font-size: 14px;
    }
</style>

<body class="bg-gray-100" x-data="{ sidebarOpen: false }">
    @extends('layouts.app')
    <div class="pb-36">
        @include('layouts.navigation')
    </div>
    @section('content')
        <div class="antialiased sans-serif h-screen">
            <div class="flex flex-col items-center ">
                <!-- กล่องแสดงสถานะการจอง -->
                <div class="flex items-center space-x-4 mb-4">
                    <p class="text-sm font-medium flex items-center">
                        <span class="w-4 h-4 inline-block bg-yellow-400 rounded-full mr-2"></span>
                        หมายถึงสีสถานะการจองของผู้ใช้ท่านอื่น
                    </p>
                    <p class="text-sm font-medium flex items-center">
                        <span class="w-4 h-4 inline-block bg-blue-400 rounded-full mr-2"></span>
                        หมายถึงสีสถานะการจองของตัวเอง
                    </p>
                </div>
                <!-- ปฏิทิน -->
                <div id="calendar" class="mx-auto py-2 md:py-24 rounded-lg shadow-md p-6">
                </div>
            </div>
            <!-- Modal for Event Details -->
            <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="eventModalLabel">รายละเอียดการจอง</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="title-div">
                                <h4 id="eventTitle"></h4>
                            </div>
                            <p><strong>ห้องประชุม:</strong> <span id="eventRoom"></span></p>
                            <p><strong>ผู้จอง:</strong> <span id="eventUser"></span></p>
                            <p><strong>วันที่จอง:</strong> <span id="eventDate"></span></p>
                            <p><strong>เวลาเริ่ม:</strong> <span id="eventStartTime"></span></p>
                            <p><strong>เวลาสิ้นสุด:</strong> <span id="eventEndTime"></span></p>
                            <p><strong>รายละเอียด:</strong> <span id="eventDetails"></span></p>
                            <p><strong>เบอร์ติดต่อ:</strong> <span id="eventContact"></span></p>
                            <p><strong>สถานะการจอง:</strong> <span id="eventStatus"></span></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="" data-bs-dismiss="modal">ปิด</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
</body>
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'th', // ใช้ภาษาไทย
                initialView: 'dayGridMonth', // เริ่มต้นแสดงเป็นเดือน
                eventClass: 'my-event-class',
                eventTextColor: 'black', //
                eventBackgroundColor: '#FF66CC',
                events: '/get-events',
                eventDidMount: function(info) {
                    let eventType = info.event.extendedProps.labelType; // ประเภทของป้าย
                    let labelColors = {
                        "red": "#ff4d4d", // สีแดง
                        "green": "#28a745", // สีเขียว
                        "blue": "#007bff", // สีฟ้า
                        "yellow": "#ffc107", // สีเหลือง
                        "gray": "#6c757d" // สีเทา
                    };

                    let eventUserId = info.event.extendedProps.user_id;
                    let currentUserId = @json(auth()->id());

                    // เปลี่ยนสีพื้นหลังของป้ายตามประเภท
                    let eventColor = labelColors[eventType] || "#dcdcdc";

                    // ถ้าผู้ใช้ที่ล็อกอินเป็นเจ้าของอีเวนต์ จะเปลี่ยนสีให้แตกต่าง
                    if (eventUserId == currentUserId) {
                        eventColor = "#007bff"; // สีฟ้าสำหรับเจ้าของอีเวนต์
                        info.el.style.color = "white";
                    } else {
                        eventColor = "yellow"; // สีเหลืองสำหรับอีเวนต์ของคนอื่น
                        info.el.style.color = "black";
                    }

                    // ใช้ CSS เปลี่ยนสีพื้นหลังของอีเวนต์
                    info.el.style.backgroundColor = eventColor;
                    info.el.style.borderRadius = "10px";
                    info.el.style.padding = "5px 8px";
                    info.el.style.textAlign = "center";

                    // เพิ่ม Tooltip แสดงรายละเอียด
                    info.el.setAttribute('title', info.event.title + " (" + eventType + ")");
                },
                headerToolbar: { // เพิ่มส่วนหัวสำหรับการเลือกมุมมอง (เช่น เดือน, สัปดาห์, วัน)
                    left: 'prev,next today', // ปุ่มก่อนหน้า, ถัดไป, วันนี้
                    center: 'title', // ชื่อเดือน
                    right: 'dayGridMonth,timeGridWeek,timeGridDay', // ปุ่มมุมมองเดือน, สัปดาห์, วัน
                },
                themeSystem: 'bootstrap5',
                buttonText: {
                    today: 'วันนี้',
                    month: 'เดือน',
                    week: 'สัปดาห์',
                    day: 'วัน',
                },
                eventClick: function(info) {
                    // ฟังก์ชันจัดการการคลิก event
                    var modal = new bootstrap.Modal(document.getElementById('eventModal'));
                    // เปิด Modal
                    modal.show();
                    document.getElementById('eventModal').style.display = 'block';

                    // อัพเดตข้อมูลใน Modal
                    document.getElementById('eventTitle').textContent = info.event.title;
                    document.getElementById('eventRoom').textContent = info.event.extendedProps.room;
                    document.getElementById('eventUser').textContent = info.event.extendedProps
                        .username;
                    document.getElementById('eventDate').textContent = info.event.extendedProps
                        .book_date;
                    document.getElementById('eventStartTime').textContent = info.event.extendedProps
                        .start_time;
                    document.getElementById('eventEndTime').textContent = info.event.extendedProps
                        .end_time;
                    document.getElementById('eventDetails').textContent = info.event.extendedProps
                        .bookdetail;
                    document.getElementById('eventContact').textContent = info.event.extendedProps
                        .booktel;
                    document.getElementById('eventStatus').textContent = info.event.extendedProps
                        .bookstatus;
                    document.getElementById('closeModalButton')?.addEventListener('click', function() {
                        modal.hide();
                    });

                }
            });
            calendar.render();
            // ฟังก์ชันแสดง Modal
            function openEventModal(info) {
                const modalData = document.querySelector('[x-data]');
                modalData.__x.$data.open = true;

                // Set modal content using Alpine.js reactive properties
                modalData.__x.$data.eventTitle = info.event.title;
                modalData.__x.$data.eventRoom = info.event.extendedProps.room;
                modalData.__x.$data.eventUser = info.event.extendedProps.username || '-';
                modalData.__x.$data.eventDescription = info.event.extendedProps.bookdetail || '-';
                modalData.__x.$data.eventContact = info.event.extendedProps.booktel || '-';
                modalData.__x.$data.eventStatus = info.event.extendedProps.bookstatus || '-';

                const eventDate = new Date(info.event.start).toLocaleDateString('th-TH', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                document.querySelector('[x-data]').__x.$data.eventDate = eventDate;

                const startTime = new Date(info.event.start).toLocaleTimeString('th-TH', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                });
                const endTime = new Date(info.event.end).toLocaleTimeString('th-TH', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                });
                modalData.__x.$data.eventTime = `${startTime} - ${endTime}`;
            }
            // Function to show the alert modal
            function showAlertModal(info) {
                const alertModal = document.querySelector('[x-data]');
                alertModal.__x.$data.alertOpen = true;
                alertModal.__x.$data.alertMessage = `คุณได้คลิกที่การจอง: ${info.event.title}`;
            }
            // โหลดรายการจองล่าสุด
            function loadLatestBookings() {
                fetch("{{ route('get-events') }}")
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        data.sort((a, b) => new Date(a.start) - new Date(b.start));
                        const latestBookings = data.slice(0, 5);
                        let html = latestBookings.length ? '' :
                            '<div class="text-center">ไม่พบข้อมูลการจอง</div>';

                        latestBookings.forEach(booking => {
                            const startDate = new Date(booking.start);
                            const formattedDate = startDate.toLocaleDateString('th-TH', {
                                day: 'numeric',
                                month: 'short',
                                year: 'numeric'
                            });
                            const startTime = startDate.toLocaleTimeString('th-TH', {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false
                            });
                            html += `
                                <div class="card mb-2">
                                    <div class="card-body p-3">
                                        <h6 class="card-title">${booking.title}</h6>
                                        <div class="small text-muted">
                                            <div><strong>ห้อง:</strong> ${booking.extendedProps.room}</div>
                                            <div><strong>วันที่:</strong> ${formattedDate}</div>
                                            <div><strong>เวลา:</strong> ${startTime}</div>
                                            <div><strong>ผู้จอง:</strong> ${booking.extendedProps.username || '-'}</div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });

                        document.getElementById('latest-bookings').innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Error fetching bookings:', error);
                        document.getElementById('latest-bookings').innerHTML =
                            '<div class="text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>';
                    });
            }

            // loadLatestBookings();

            // อัปเดตปฏิทินเมื่อเปลี่ยนค่าห้อง
            document.getElementById('month-view').addEventListener('click', function() {
                calendar.changeView('dayGridMonth');
                calendar.refetchEvents();
            });

            document.getElementById('week-view').addEventListener('click', function() {
                calendar.changeView('timeGridWeek');
                calendar.refetchEvents();
            });

            document.getElementById('day-view').addEventListener('click', function() {
                calendar.changeView('timeGridDay');
                calendar.refetchEvents();
            });
        });
    </script>
@endsection

</html>

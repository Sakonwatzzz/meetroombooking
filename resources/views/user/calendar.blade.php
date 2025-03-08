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
    <link rel="stylesheet" href="{{ mix('resources/css/app.css') }}">

    <!-- DNS Prefetch (ไม่จำเป็นต้องใส่สำหรับ unpkg และ jsdelivr) -->

    <script>
        let currentUserId = @json(auth()->id());
    </script>
</head>

<style>
    /* สไตล์หลักสำหรับระบบการจองห้องประชุม */
    .my-event-class {
        border-radius: 8px;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .my-event-class:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Modal Header สไตล์ */
    .modal-header {
        background: linear-gradient(to right, #2c3e50, #3498db);
        padding: 18px 25px;
        border-radius: 12px 12px 0 0;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .modal-header h5 {
        color: #ffffff;
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
        letter-spacing: 0.5px;
    }

    .modal-header .btn-close {
        color: white;
        opacity: 0.8;
        transition: opacity 0.2s;
    }

    .modal-header .btn-close:hover {
        opacity: 1;
    }

    /* Modal สไตล์หลัก */
    .modal {
        display: none;
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(5px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1050;
    }

    .modal-content {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        width: 550px;
        max-width: 90%;
        position: relative;
        animation: modalFadeIn 0.4s ease-out;
        border: none;
    }

    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Modal Body สไตล์ */
    .modal-body {
        padding: 25px 30px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .title-div {
        margin-bottom: 15px;
        border-bottom: 1px solid #eaeaea;
        padding-bottom: 15px;
    }

    .title-div h4 {
        font-size: 1.4rem;
        color: #2c3e50;
        font-weight: 600;
        margin: 0;
        word-wrap: break-word;
    }

    .modal-body p {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        font-size: 1.05rem;
        padding: 10px;
        margin: 0;
        border-radius: 8px;
        color: #333;
        background-color: #f8f9fa;
        transition: background-color 0.3s ease;
    }

    .modal-body p:hover {
        background-color: #f0f4f8;
    }

    .modal-body strong {
        font-weight: 600;
        color: #2c3e50;
        min-width: 120px;
    }

    .modal-body span {
        font-weight: 400;
        color: #3a3a3a;
        text-align: right;
        flex-grow: 1;
        word-break: break-word;
    }

    /* ปรับแต่งสถานะการจอง */
    #eventStatus {
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-block;
    }

    /* สีสถานะต่างๆ (คุณสามารถปรับใช้ในโค้ด JavaScript) */
    .status-confirmed {
        background-color: #d4edda;
        color: #155724;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-canceled {
        background-color: #f8d7da;
        color: #721c24;
    }

    /* Close Button สไตล์ */
    .close-btn {
        color: white;
        font-size: 24px;
        font-weight: bold;
        position: absolute;
        top: 15px;
        right: 20px;
        opacity: 0.7;
        transition: all 0.2s;
    }

    .close-btn:hover {
        color: #fff;
        opacity: 1;
        cursor: pointer;
    }

    /* Modal Footer สไตล์ */
    .modal-footer {
        padding: 15px 25px;
        border-top: 1px solid #eaeaea;
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    .modal-footer button {
        padding: 10px 25px;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .modal-footer button[data-bs-dismiss="modal"] {
        background-color: #3498db;
        color: white;
    }

    .modal-footer button[data-bs-dismiss="modal"]:hover {
        background-color: #2980b9;
        transform: translateY(-2px);
    }

    /* เพิ่ม Responsive */
    @media (max-width: 576px) {
        .modal-content {
            width: 95%;
        }

        .modal-body p {
            flex-direction: column;
            align-items: flex-start;
        }

        .modal-body strong {
            margin-bottom: 5px;
        }

        .modal-body span {
            width: 100%;
            text-align: left;
        }
    }

    /* เพิ่ม CSS เพื่อปรับแต่งส่วนของปฏิทิน FullCalendar */
    .fc .fc-toolbar-title {
        font-size: 1.5rem;
        color: #2c3e50;
        font-weight: 600;
    }

    .fc .fc-button-primary {
        background-color: #3498db;
        border-color: #3498db;
        transition: all 0.3s ease;
    }

    .fc .fc-button-primary:hover {
        background-color: #2980b9;
        border-color: #2980b9;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active {
        background-color: #2c3e50;
        border-color: #2c3e50;
    }

    .fc-day-today {
        background-color: rgba(52, 152, 219, 0.1) !important;
    }

    .booking-card {
        border-radius: 8px;
        transition: all 0.3s ease;
        border: 1px solid #eaeaea;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .booking-card .card-title {
        color: #2c3e50;
        font-weight: 600;
    }

    .booking-card .text-muted strong {
        color: #34495e;
        font-weight: 600;
    }

    /* สไตล์สำหรับสถานะในรายการการจองล่าสุด */
    .booking-card .status-confirmed {
        background-color: #d4edda;
        color: #155724;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .booking-card .status-pending {
        background-color: #fff3cd;
        color: #856404;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .booking-card .status-canceled {
        background-color: #f8d7da;
        color: #721c24;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 500;
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
            <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" inert="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="eventModalLabel">รายละเอียดการจองห้องประชุม</h5>
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
                            <p><strong>สถานะการจอง:</strong> <span id="eventStatus" class="status-confirmed"></span></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn" data-bs-dismiss="modal">ปิด</button>
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
                eventClassNames: 'my-event-class',
                eventTextColor: 'black',
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
                    info.el.style.borderRadius = "8px"; // ปรับให้เข้ากับสไตล์ใหม่
                    info.el.style.padding = "5px 8px";
                    info.el.style.textAlign = "center";
                    info.el.style.boxShadow = "0 2px 4px rgba(0, 0, 0, 0.1)"; // เพิ่มเงาเล็กน้อย
                    info.el.style.transition = "all 0.3s ease"; // เพิ่ม transition สำหรับ hover effect

                    // เพิ่ม hover effect โดยใช้ addEventListener
                    info.el.addEventListener('mouseenter', function() {
                        this.style.transform = "translateY(-2px)";
                        this.style.boxShadow = "0 4px 8px rgba(0, 0, 0, 0.15)";
                    });

                    info.el.addEventListener('mouseleave', function() {
                        this.style.transform = "translateY(0)";
                        this.style.boxShadow = "0 2px 4px rgba(0, 0, 0, 0.1)";
                    });

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

                    // อัพเดตสถานะและเพิ่ม class ตามสถานะ
                    const eventStatus = info.event.extendedProps.bookstatus;
                    const statusElement = document.getElementById('eventStatus');

                    // ลบคลาสเดิมทั้งหมด
                    statusElement.classList.remove('status-confirmed', 'status-pending',
                        'status-canceled');

                    // เพิ่มคลาสตามสถานะ
                    if (eventStatus.includes('อนุมัติ') || eventStatus.toLowerCase().includes(
                            'confirmed')) {
                        statusElement.classList.add('status-confirmed');
                    } else if (eventStatus.includes('รออนุมัติ') || eventStatus.toLowerCase().includes(
                            'pending')) {
                        statusElement.classList.add('status-pending');
                    } else if (eventStatus.includes('ยกเลิก') || eventStatus.toLowerCase().includes(
                            'canceled')) {
                        statusElement.classList.add('status-canceled');
                    }

                    statusElement.textContent = eventStatus;

                    // เปิด Modal
                    modal.show();

                    // เพิ่ม event listener สำหรับปุ่มปิด (ถ้ามี)
                    const closeBtn = document.querySelector('.close-btn');
                    if (closeBtn) {
                        closeBtn.addEventListener('click', function() {
                            modal.hide();
                        });
                    }

                    // เพิ่ม event listener สำหรับปุ่มปิดใน footer
                    document.getElementById('closeModalButton')?.addEventListener('click', function() {
                        modal.hide();
                    });
                }
            });

            calendar.render();

            // Function to format date และ time ให้สวยงาม
            function formatThaiDate(dateStr) {
                const date = new Date(dateStr);
                return date.toLocaleDateString('th-TH', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }

            function formatTime(dateStr) {
                const date = new Date(dateStr);
                return date.toLocaleTimeString('th-TH', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                });
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

                            // สร้าง class สำหรับแสดงสถานะด้วยสี
                            let statusClass = '';
                            const status = booking.extendedProps.bookstatus;

                            if (status.includes('อนุมัติ') || status.toLowerCase().includes(
                                'confirmed')) {
                                statusClass = 'status-confirmed';
                            } else if (status.includes('รออนุมัติ') || status.toLowerCase().includes(
                                    'pending')) {
                                statusClass = 'status-pending';
                            } else if (status.includes('ยกเลิก') || status.toLowerCase().includes(
                                    'canceled')) {
                                statusClass = 'status-canceled';
                            }

                            html += `
                                <div class="card mb-2 booking-card">
                                    <div class="card-body p-3">
                                        <h6 class="card-title">${booking.title}</h6>
                                        <div class="small text-muted">
                                            <div><strong>ห้อง:</strong> ${booking.extendedProps.room}</div>
                                            <div><strong>วันที่:</strong> ${formattedDate}</div>
                                            <div><strong>เวลา:</strong> ${startTime}</div>
                                            <div><strong>ผู้จอง:</strong> ${booking.extendedProps.username || '-'}</div>
                                            <div><strong>สถานะ:</strong> <span class="${statusClass}">${status}</span></div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });

                        document.getElementById('latest-bookings').innerHTML = html;

                        // เพิ่ม animation และ hover effects ให้กับการ์ดการจอง
                        const bookingCards = document.querySelectorAll('.booking-card');
                        bookingCards.forEach(card => {
                            card.style.transition = "all 0.3s ease";

                            card.addEventListener('mouseenter', function() {
                                this.style.transform = "translateY(-3px)";
                                this.style.boxShadow = "0 6px 12px rgba(0, 0, 0, 0.1)";
                            });

                            card.addEventListener('mouseleave', function() {
                                this.style.transform = "translateY(0)";
                                this.style.boxShadow = "0 1px 3px rgba(0, 0, 0, 0.1)";
                            });
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching bookings:', error);
                        document.getElementById('latest-bookings').innerHTML =
                            '<div class="text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>';
                    });
            }

            // อัปเดตปฏิทินเมื่อเปลี่ยนค่าห้อง
            document.getElementById('month-view')?.addEventListener('click', function() {
                calendar.changeView('dayGridMonth');
                calendar.refetchEvents();
            });

            document.getElementById('week-view')?.addEventListener('click', function() {
                calendar.changeView('timeGridWeek');
                calendar.refetchEvents();
            });

            document.getElementById('day-view')?.addEventListener('click', function() {
                calendar.changeView('timeGridDay');
                calendar.refetchEvents();
            });

            // โหลดรายการจองล่าสุดเมื่อโหลดหน้า (uncomment ถ้าต้องการใช้งาน)
            // loadLatestBookings();
        });
    </script>
@endsection

</html>

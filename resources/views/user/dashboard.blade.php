<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/locales/th.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.css" rel="stylesheet">
</head>
<style>
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

    /* ปรับแต่งป้ายอีเวนต์ให้เป็นโค้งมน */
    .fc-event {
        border-radius: 12px !important;
        padding: 6px 10px !important;
        font-weight: bold;
        font-size: 14px;
    }
</style>

<body x-data="{ sidebarOpen: false }" class="bg-gray-100">
    @extends('layouts.app')
    <div class="pb-32">
        @include('layouts.navigation')
    </div>
    @section('content')
        <div class="max-w-6xl mx-auto pb-20">
            <h1 class="text-2xl font-bold mb-4">📅 Dashboard - การจองห้องประชุมสำหรับผู้ใช้</h1>
            <!-- ห้องที่พร้อมให้จอง -->

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <p class="text-sm font-medium flex items-center">
                    <span class="w-4 h-4 inline-block bg-yellow-400 rounded-full mr-2"></span>
                    หมายถึงสีสถานะการจองของผู้ใช้ท่านอื่น
                </p>
                <p class="text-sm font-medium flex items-center">
                    <span class="w-4 h-4 inline-block bg-blue-400 rounded-full mr-2"></span>
                    หมายถึงสีสถานะการจองของตัวเอง
                </p>
                <!-- ปฏิทิน -->
                <div id="calendar" class="p-4 w-[90%] rounded-xl shadow-md md:col-span-2">
                </div>

                <div class="bg-white p-4 rounded-xl shadow-md">
                    <h2 class="text-lg font-semibold pb-4">📌 การจองของฉัน</h2>
                    <a href="{{ route('rooms.index') }}"
                        class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center justify-center space-x-2 ">
                        <img src="{{ asset('images/next.png') }}" class="w-7 h-7 filter invert brightness-100" alt="Next Icon">
                        <span>Meet Room List</span>
                    </a>
                    <ul class="text-sm text-gray-600 mt-2">
                        @forelse ($bookings as $booking)
                            <li> {{ $booking->book_date }} - {{ $booking->room->room_name ?? 'ไม่ระบุห้อง' }} -
                                {{ $booking->start_time }} -
                                {{ $booking->end_time }}</li>
                        @empty
                            <li class="text-gray-400">ไม่มีการจอง</li>
                        @endforelse
                    </ul>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-md">
                    <h2 class="text-lg font-semibold">🏢 ห้องประชุมที่พร้อมจอง</h2>
                    <ul id="roomList" class="text-sm text-gray-600 mt-2 pb-2">
                        <!-- ข้อมูลห้องประชุมจะแสดงที่นี่ -->
                    </ul>
                </div>
            </div>
        </div>
    @endsection
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
                    },
                    themeSystem: 'bootstrap5', // ใช้ธีม Bootstrap 5
                    // editable: true, // ให้สามารถลากและวาง event ได้
                    // droppable: true, // สามารถลาก event ไปยังวันที่ใหม่ได้
                    dayCellClassNames: 'text-center py-2', // ตั้งค่าให้วันในปฏิทินมีข้อความที่จัดกึ่งกลาง
                    eventsSet: function() {
                        // ฟังก์ชันที่จะถูกเรียกเมื่อ event ถูกตั้งค่าใหม่
                        console.log('Events loaded');
                    }
                });
                calendar.render();;
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
        <script>
            fetch('/api/my-bookings')
                .then(response => response.json())
                .then(data => {
                    // แสดงข้อมูลการจอง
                    const myBookingsList = document.getElementById("myBookingsList");
                    myBookingsList.innerHTML = '';

                    // เพิ่มลิงก์ไปยังหน้า rooms
                    const link = document.createElement('a');
                    link.href = data.rooms_url;
                    link.textContent = "Go to Room List";
                    myBookingsList.appendChild(link);

                    // แสดงการจอง
                    data.bookings.forEach(booking => {
                        const listItem = document.createElement("li");
                        listItem.innerHTML =
                            `🔹 ${booking.room_name ?? 'ไม่ระบุห้อง'} - ${booking.start_time} - ${booking.end_time}`;
                        myBookingsList.appendChild(listItem);
                    });
                })
                .catch(error => {
                    console.error("Error fetching bookings:", error);
                });
        </script>
        <script>
            // ฟังก์ชันสำหรับดึงข้อมูลห้องประชุมจาก API
            function fetchAvailableRooms() {
                fetch('/api/rooms/available') // API ที่ดึงข้อมูลห้องประชุมที่พร้อมจอง
                    .then(response => response.json()) // แปลงข้อมูลเป็น JSON
                    .then(data => {
                        const roomList = document.getElementById('roomList');
                        roomList.innerHTML = ''; // เคลียร์รายการเดิม

                        // นับจำนวนห้องที่พร้อมใช้งาน
                        const availableRooms = data.filter(room => room.room_status === 'available');

                        if (availableRooms.length === 0) {
                            roomList.innerHTML = '<li class="text-gray-400">ไม่มีห้องประชุมที่พร้อมใช้งาน</li>';
                        } else {
                            // แสดงจำนวนห้องที่พร้อมใช้งาน
                            roomList.innerHTML =
                                `<li class="text-green-500 text-2xl">มี ${availableRooms.length} ห้องประชุมที่พร้อมให้จอง</li>`;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching rooms:', error);
                        const roomList = document.getElementById('roomList');
                        roomList.innerHTML = '<li class="text-gray-400">เกิดข้อผิดพลาดในการดึงข้อมูลห้องประชุม</li>';
                    });
            }

            // เรียกฟังก์ชันดึงข้อมูลห้องประชุมเมื่อโหลดหน้า
            document.addEventListener('DOMContentLoaded', fetchAvailableRooms);
        </script>
    @endsection
</body>

</html>

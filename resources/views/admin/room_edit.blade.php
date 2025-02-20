<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room</title>

    <!-- Load Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-md shadow-md">
        <h2 class="text-2xl font-semibold mb-4">Edit Room</h2>

        <form id="editRoomForm" enctype="multipart/form-data" onsubmit="event.preventDefault(); updateRoom();">
            @csrf
            <!-- Hidden input for room_id -->
            <input type="hidden" id="room_id">

            <!-- Room Name Field -->
            <div class="mb-4">
                <label for="room_name" class="block text-gray-700 font-medium mb-2">Room Name</label>
                <input type="text" id="room_name" name="room_name" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Enter room name">
            </div>

            <!-- Room Details Field -->
            <div class="mb-4">
                <label for="room_detail" class="block text-gray-700 font-medium mb-2">Room Details</label>
                <textarea id="room_detail" name="room_detail" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-h-[100px]"
                    placeholder="Enter room details"></textarea>
            </div>

            <!-- Current Picture Display -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Current Picture</label>
                <div id="current_room_pic" class="mb-2 max-w-md">
                    <img id="current_pic_preview" class="hidden w-full h-auto rounded-lg shadow-md"
                        alt="Current room picture">
                </div>
            </div>

            <!-- New Picture Upload -->
            <div class="mb-4">
                <label for="room_pic" class="block text-gray-700 font-medium mb-2">Update Room Picture</label>
                <input type="file" id="room_pic" name="room_pic" accept="image/jpeg,image/png,image/gif"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    onchange="previewImage(this)">
                <div id="new_pic_preview" class="mt-2 hidden max-w-md">
                    <img class="w-full h-auto rounded-lg shadow-md" alt="New room picture preview">
                </div>
                <p class="text-sm text-gray-500 mt-1">Accepted formats: JPEG, PNG, GIF (max 2MB)</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between mt-6">
                <a href="{{ route('admin.room.list') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md transition duration-150 ease-in-out">
                    Cancel
                </a>
                <button type="submit" onclick="console.log('🔹 Update Room Function Called!'); updateRoom();"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md transition duration-150 ease-in-out">
                    Update Room
                </button>
            </div>
        </form>

        <p id="responseMessage" class="mt-4 text-green-500 hidden"></p>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const roomId = window.location.pathname.split('/').pop();

        document.addEventListener("DOMContentLoaded", function() {
            loadRoomData();
        });

        function loadRoomData() {
            let token = localStorage.getItem('admin_token');
            if (!token) {
                window.location.href = "/admin/login";
                return;
            }

            axios.get(`/api/admin/rooms/${roomId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            }).then(response => {
                let room = response.data;
                document.getElementById('room_name').value = room.room_name;
                document.getElementById('room_detail').value = room.room_detail;

                let roomPicHTML = room.room_pic ?
                    `<img src="/storage/${room.room_pic}" alt="Room Image" class="w-32 h-32 object-cover rounded-md">` :
                    `<span class="text-gray-500">No Image</span>`;

                document.getElementById('current_room_pic').innerHTML = roomPicHTML;

            }).catch(error => {
                console.error("Error loading room:", error);
                alert("Failed to load room details. Redirecting...");
                window.location.href = "/admin/room_list";
            });
        }

        // function updateRoom() {
        //     console.log("updateRoom() function called!"); 

        async function updateRoom() {
            // Show console log for debugging
            console.log('🔹 Starting updateRoom function');

            // Get the authentication token
            const token = localStorage.getItem('admin_token');
            if (!token) {
                alert('Please login first');
                window.location.href = '/admin/login';
                return;
            }

            try {
                // Create FormData object
                const formData = new FormData();

                // Get form values
                const roomName = document.getElementById('room_name').value;
                const roomDetail = document.getElementById('room_detail').value;
                const roomPic = document.getElementById('room_pic').files[0];

                // Log form data for debugging
                console.log('📝 Form Data:', {
                    roomName,
                    roomDetail,
                    hasPic: !!roomPic
                });

                // Add form fields to FormData
                formData.append('room_name', roomName);
                formData.append('room_detail', roomDetail);
                if (roomPic) {
                    formData.append('room_pic', roomPic);
                }

                // Add method override for PUT request
                formData.append('_method', 'PUT');

                // Show loading state
                const submitButton = document.querySelector('button[type="submit"]');
                const originalButtonText = submitButton.innerHTML;
                submitButton.innerHTML = 'Updating...';
                submitButton.disabled = true;

                // Make API request
                const response = await axios({
                    method: 'post',
                    url: `/api/admin/rooms/${roomId}`,
                    data: formData,
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'multipart/form-data',
                        'Accept': 'application/json'
                    }
                });

                // Handle success
                console.log('✅ Room updated successfully:', response.data);

                // Show success message
                const messageElement = document.getElementById('responseMessage');
                messageElement.textContent = 'Room updated successfully!';
                messageElement.classList.remove('hidden');
                messageElement.classList.remove('text-red-500');
                messageElement.classList.add('text-green-500');

                // Redirect after short delay
                setTimeout(() => {
                    window.location.href = '/admin/room_list';
                }, 1500);

            } catch (error) {
                // Handle errors
                console.error('❌ Error updating room:', error);

                // Show error message
                const messageElement = document.getElementById('responseMessage');
                messageElement.textContent = error.response?.data?.message || 'Error updating room. Please try again.';
                messageElement.classList.remove('hidden');
                messageElement.classList.remove('text-green-500');
                messageElement.classList.add('text-red-500');

                // Reset button state
                submitButton.innerHTML = originalButtonText;
                submitButton.disabled = false;
            }
        }
    </script>


</body>

</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Vero Web Portal Task</title>
</head>
<body>
    <div class="w-full min-h-screen mx-auto max-w-7xl px-2 py-12">
        <div class="py-2 flex justify-between items-center">
            <h1 class="text-4xl font-bold">Dataset</h1>
            <div>
                <input id="searchForm" type="text" class="w-52 p-2 border border-gray-300 rounded" placeholder="Search here...">
                <button class="bg-blue-600 py-2 px-4 rounded text-white openModal">Modal Image</button>
            </div>
        </div>
        
        <div class="mt-3">
            <table class="w-full table">
                <thead class="bg-blue-600 text-white">
                    <th class="p-2 w-10 text-center">#</th>
                    <th class="p-2 text-left">Task</th>
                    <th class="p-2 text-left">Title</th>
                    <th class="p-2 text-left">Description</th>
                    <th class="p-2 text-left">Color Code</th>
                </thead>
                <tbody id="isLoadingData">
                    <tr>
                        <td class="py-6 text-center" colspan="5">
                            Loading data...
                        </td>
                    </tr>
                </tbody>
                <tbody id="datasetTable" style="display:none"></tbody>
            </table>
        </div>
    </div>

    <div class="relative z-10" id="modal" style="display:none" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
    
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                    <div class="bg-white p-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center w-full sm:ml-4 sm:mt-0 sm:text-left">
                                {{-- <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Select Image</h3> --}}
                                <div class="mt-3 flex justify-center w-full">
                                    <label for="selectImage" class="bg-blue-600 text-white py-2 px-6 rounded cursor-pointer hover:bg-blue-700">Click here to select image</label>
                                    <input hidden accept="image/*" id="selectImage" onchange="loadImg(event)" type="file" name="" id="">
                                </div>
                                <div class="mt-3">
                                    <img src="" id="preview" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" class="mt-3 closeModal inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
    <script>

        window.onload = onloadFunc();

        $(".openModal").on('click', function() {
            $("#modal").show();
        });

        $(".closeModal").on('click', function() {
            $("#modal").hide();
        });

        function loadImg(e) {
            var output = document.getElementById('preview');
            output.src = URL.createObjectURL(e.target.files[0]);
            output.onload = function() {
                URL.revokeObjectURL(output.src)
            }
        };

        function onloadFunc () {
            loadData();

            // set interval for an hour
            setInterval(() => {
                loadData() 
            }, 3600000);
        }

        async function loadData(q) {
            $("#datasetTable").hide();
            $("#isLoadingData").show();
            await $.ajax({
                url: "{{route('ajax.get')}}",
                data: {q: q},
                method: "GET",
                success: function(res){
                    var tableTr = [];
                    for(let i = 0; i < res.data.length; i++) {
                        var table = tableDom(res.data[i], i)
                        tableTr.push(table);
                    }
                    $("#isLoadingData").hide();
                    $("#datasetTable").show();
                    $("#datasetTable").empty();
                    $("#datasetTable").append(tableTr);
                },
            })
        }

        function tableDom(data, i) {
            return `
                <tr>
                    <td class="p-2 border">${i+1}</td>
                    <td class="p-2 border">${data?.task}</td>
                    <td class="p-2 border" style="width: 20%">${data?.title}</td>
                    <td class="p-2 border" style="width: 50%">${data?.description}</td>
                    <td class="p-2 border" style="color: ${data?.colorCode}">${data?.colorCode}</td>
                </tr>
            `
        }

        $("#searchForm").on('input' , function() {
            let q = $(this).val();
            loadData(q);
        })

    </script>
</body>
</html>
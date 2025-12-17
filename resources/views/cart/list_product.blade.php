<x-app-layout>
    <x-slot name="app_asset">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    </x-slot>

    <div class="container">

        <label>Radius</label>
        <input type="number" name="radius" id="radius" min="1" max="15" value="10">
        <button class="btn btn-primary" id="locateMe">Locate Me</button>

        <div class="row mt-3">
            <div class="col-md-8">
                <div id="map" style="height: 500px;"></div>
            </div>

            <div class="col-md-4" id="product_list"></div>
        </div>

        <script>
            // Inisialisasi peta
            var map = L.map('map').setView([-6.200000, 106.816666], 13); // Default: Jakarta
            var userMarker;
            var markersLayer = L.layerGroup().addTo(map);

            // Tambahkan tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(map);

            // Fungsi fetch produk berdasarkan lokasi
            function fetchProducts(lat, lng, rad) {
                markersLayer.clearLayers();

                fetch(`/api/products?latitude=${lat}&longitude=${lng}&radius=${rad}`)
                    .then(response => response.json())
                    .then(data => {
                        let product_list_html = "";

                        data.forEach(product => {
                            const marker = L.marker([product.latitude, product.longitude])
                                .bindPopup(`<b>${product.name}</b><br>${product.detail}`);

                            markersLayer.addLayer(marker);

                            let price_formatted = new Intl.NumberFormat().format(product.price);

                            product_list_html += `
                                <div class="row m-2 p-2 bg-white border rounded shadow-sm">
                                    <h5>${product.name}</h5>
                                    <p class="text-justify mb-0">${product.detail}</p>
                                    <h5 class="mr-1">Rp. ${price_formatted}</h5>

                                    <div class="d-flex flex-column mt-2">
                                        <form action="{{ route('cart.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="${product.id}">

                                            <div class="input-group mb-3">
                                                <input type="number" name="quantity" 
                                                       class="form-control" value="1" min="1">
                                                <button type="submit" class="btn btn-primary">
                                                    Add to Cart
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>`;
                        });

                        document.getElementById("product_list").innerHTML = product_list_html;
                    });
            }

            // Tombol "Locate Me"
            document.getElementById('locateMe').addEventListener('click', () => {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const { latitude, longitude } = position.coords;
                            let radius_user = document.getElementById("radius").value;

                            map.setView([latitude, longitude], 13);

                            if (userMarker) map.removeLayer(userMarker);

                            userMarker = L.marker([latitude, longitude]).addTo(map)
                                .bindPopup('<b>Your Location</b>')
                                .openPopup();

                            fetchProducts(latitude, longitude, radius_user);
                        },
                        (error) => {
                            alert('Error mendapatkan lokasi Anda: ' + error.message);
                        }
                    );
                } else {
                    alert('Geolocation tidak didukung browser Anda.');
                }
            });

            // Fetch awal dengan lokasi default
            fetchProducts(-6.200000, 106.816666, 10);
        </script>
    </div>
</x-app-layout>

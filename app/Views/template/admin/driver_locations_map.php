<?= $this->extend('template/admin/main') ?>

<?= $this->section('content') ?>
<?= $this->include('common/message') ?>

<div class="page-header">
    <div class="header-wrapper row m-0">
        <div class="col-auto p-0">
            <h3 class="page-title">All Drivers (Live Locations)</h3>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Driver Live Tracking Map</h5>
            </div>
            <div class="card-body">
                <div id="map" style="height: 80vh; width: 100%; border-radius: 10px;"></div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('js') ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= getenv('google.maps.key') ?>"></script>
<script>
    let map;
    let markers = {};
    let infoWindows = {};

    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: 9.0820, lng: 8.6753 }, // Nigeria
            zoom: 6
        });

        fetchDrivers();
        setInterval(fetchDrivers, 10000); // Refresh every 10s
    }

    function fetchDrivers() {
        fetch("<?= base_url('admin/fetch-driver-locations') ?>")
            .then(res => res.json())
            .then(drivers => {
                if (!Array.isArray(drivers)) {
                    console.error("Invalid driver data:", drivers);
                    return;
                }

                drivers.forEach(driver => {
                    if (!driver.latitude || !driver.longitude) return;

                    const pos = {
                        lat: parseFloat(driver.latitude),
                        lng: parseFloat(driver.longitude)
                    };
                    const fullName = `${driver.first_name} ${driver.last_name}`;
                    const status = driver.status ?? 'Unknown';
                    const updatedAt = driver.updated_at ?? 'N/A';

                    // Create new marker if it doesn't exist
                    if (!markers[driver.employee_id]) {
                        const marker = new google.maps.Marker({
                            position: pos,
                            map,
                            title: fullName,
                            icon: status.toLowerCase() === 'online'
                                ? "https://maps.google.com/mapfiles/ms/icons/green-dot.png"
                                : "https://maps.google.com/mapfiles/ms/icons/red-dot.png"
                        });

                        // Info window for hover/click
                        const infoContent = `
                            <div style="font-size:13px;">
                                <strong>${fullName}</strong><br>
                                Status: <b style="color:${status.toLowerCase() === 'online' ? 'green' : 'red'}">${status}</b><br>
                                Last Update: ${updatedAt}<br>
                                <a href="<?= base_url('admin/driver/') ?>${driver.employee_id}" 
                                   style="color:#007bff; text-decoration:none; font-weight:500;">View Live</a>
                            </div>
                        `;
                        const infoWindow = new google.maps.InfoWindow({ content: infoContent });

                        marker.addListener("click", () => {
                            infoWindow.open(map, marker);
                            setTimeout(() => {
                                window.location.href = "<?= base_url('admin/driver/') ?>" + driver.employee_id;
                            }, 2000);
                        });

                        marker.addListener("mouseover", () => infoWindow.open(map, marker));
                        marker.addListener("mouseout", () => infoWindow.close());

                        markers[driver.employee_id] = marker;
                        infoWindows[driver.employee_id] = infoWindow;
                    } else {
                        // Update existing marker position
                        markers[driver.employee_id].setPosition(pos);
                    }
                });
            })
            .catch(err => console.error("Error fetching drivers:", err));
    }

    window.onload = initMap;
</script>
<?= $this->endSection() ?>
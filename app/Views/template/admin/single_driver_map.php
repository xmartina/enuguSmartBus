<?= $this->extend('template/admin/main') ?>

<?= $this->section('content') ?>
<?= $this->include('common/message') ?>

<div class="page-header">
    <div class="header-wrapper row m-0">
        <div class="col-auto p-0">
            <h3 class="page-title">Driver Live Location</h3>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <?= esc($driver->first_name . ' ' . $driver->last_name) ?> (<?= esc($driver->status) ?>)
                </h5>
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
    let map, marker, infoWindow;

    function initMap() {
        const initialPosition = {
            lat: parseFloat("<?= $driver->latitude ?>") || 9.0820,
            lng: parseFloat("<?= $driver->longitude ?>") || 8.6753
        };

        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 10,
            center: initialPosition
        });

        marker = new google.maps.Marker({
            position: initialPosition,
            map: map,
            title: "<?= esc($driver->first_name . ' ' . $driver->last_name) ?>",
            icon: "<?= $driver->status === 'online' 
                ? 'https://maps.google.com/mapfiles/ms/icons/green-dot.png' 
                : 'https://maps.google.com/mapfiles/ms/icons/red-dot.png' ?>"
        });

        infoWindow = new google.maps.InfoWindow({
            content: `
                <div style="font-size:13px;">
                    <strong><?= esc($driver->first_name . ' ' . $driver->last_name) ?></strong><br>
                    Status: <b style="color:<?= $driver->status === 'online' ? 'green' : 'red' ?>;">
                        <?= esc(ucfirst($driver->status)) ?>
                    </b><br>
                    Last Update: <?= esc($driver->updated_at ?? 'N/A') ?>
                </div>
            `
        });

        marker.addListener("click", () => infoWindow.open(map, marker));
        marker.addListener("mouseover", () => infoWindow.open(map, marker));
        marker.addListener("mouseout", () => infoWindow.close());

        // Refresh driver location every 10 seconds
        setInterval(refreshLocation, 10000);
    }

    function refreshLocation() {
        fetch("<?= base_url('admin/fetch-driver-locations') ?>")
            .then(res => res.json())
            .then(drivers => {
                const current = Array.isArray(drivers)
                    ? drivers.find(d => d.employee_id == <?= $driver->employee_id ?>)
                    : null;

                if (current && current.latitude && current.longitude) {
                    const newPos = {
                        lat: parseFloat(current.latitude),
                        lng: parseFloat(current.longitude)
                    };
                    marker.setPosition(newPos);
                    map.setCenter(newPos);

                    // Update info window
                    infoWindow.setContent(`
                        <div style="font-size:13px;">
                            <strong>${current.first_name} ${current.last_name}</strong><br>
                            Status: <b style="color:${current.status.toLowerCase() === 'online' ? 'green' : 'red'}">
                                ${current.status}
                            </b><br>
                            Last Update: ${current.updated_at ?? 'N/A'}
                        </div>
                    `);
                }
            })
            .catch(err => console.error("Error updating driver location:", err));
    }

    window.onload = initMap;
</script>
<?= $this->endSection() ?>
<?php
// services_helper.php
function getServices($db) {
    try {
        $query = "SELECT * FROM services WHERE is_active = 1 ORDER BY display_order ASC, id ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $exception) {
        error_log("Error fetching services: " . $exception->getMessage());
        return [];
    }
}

function getServiceById($db, $id) {
    try {
        $query = "SELECT * FROM services WHERE id = :id AND is_active = 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $exception) {
        error_log("Error fetching service: " . $exception->getMessage());
        return null;
    }
}

function createService($db, $data) {
    try {
        $query = "INSERT INTO services (title, description, icon, features, display_order, is_active) 
                  VALUES (:title, :description, :icon, :features, :display_order, :is_active)";
        $stmt = $db->prepare($query);
        
        $features_json = json_encode($data['features']);
        
        $stmt->bindParam(":title", $data['title']);
        $stmt->bindParam(":description", $data['description']);
        $stmt->bindParam(":icon", $data['icon']);
        $stmt->bindParam(":features", $features_json);
        $stmt->bindParam(":display_order", $data['display_order']);
        $stmt->bindParam(":is_active", $data['is_active']);
        
        return $stmt->execute();
    } catch(PDOException $exception) {
        error_log("Error creating service: " . $exception->getMessage());
        return false;
    }
}

function updateService($db, $id, $data) {
    try {
        $query = "UPDATE services SET title = :title, description = :description, icon = :icon, 
                  features = :features, display_order = :display_order, is_active = :is_active 
                  WHERE id = :id";
        $stmt = $db->prepare($query);
        
        $features_json = json_encode($data['features']);
        
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":title", $data['title']);
        $stmt->bindParam(":description", $data['description']);
        $stmt->bindParam(":icon", $data['icon']);
        $stmt->bindParam(":features", $features_json);
        $stmt->bindParam(":display_order", $data['display_order']);
        $stmt->bindParam(":is_active", $data['is_active']);
        
        return $stmt->execute();
    } catch(PDOException $exception) {
        error_log("Error updating service: " . $exception->getMessage());
        return false;
    }
}

function deleteService($db, $id) {
    try {
        $query = "DELETE FROM services WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    } catch(PDOException $exception) {
        error_log("Error deleting service: " . $exception->getMessage());
        return false;
    }
}
?>
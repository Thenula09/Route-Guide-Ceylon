<?php

class VehicleOwnerController {
    
    private $vehicleModel;
    private $routeModel;
    private $bookingModel;
    
    public function __construct() {
        require_once '../../Models/vehicleOwner/Vehicle.php';
        require_once '../../Models/common/Route.php';
        require_once '../../Models/common/Booking.php';
        
        $this->vehicleModel = new Vehicle();
        $this->routeModel = new Route();
        $this->bookingModel = new Booking();
    }
    
    // Display vehicle owner dashboard
    public function dashboard($ownerId) {
        $data = [
            'vehicles' => $this->vehicleModel->getByOwner($ownerId),
            'routes' => $this->routeModel->getByVehicleOwner($ownerId),
            'bookings' => $this->bookingModel->getVehicleBookings($ownerId),
            'earnings' => $this->calculateEarnings($ownerId)
        ];
        
        include '../../Views/vehicleOwner/vehicleOwner.html';
    }
    
    // Add new vehicle
    public function addVehicle($ownerId, $vehicleData) {
        $vehicleData['owner_id'] = $ownerId;
        $vehicleData['status'] = 'pending'; // Pending admin approval
        $vehicleData['created_at'] = date('Y-m-d H:i:s');
        
        return $this->vehicleModel->create($vehicleData);
    }
    
    // Update vehicle
    public function updateVehicle($vehicleId, $ownerId, $vehicleData) {
        $vehicle = $this->vehicleModel->getById($vehicleId);
        
        if ($vehicle && $vehicle['owner_id'] == $ownerId) {
            return $this->vehicleModel->update($vehicleId, $vehicleData);
        }
        return false;
    }
    
    // Delete vehicle
    public function deleteVehicle($vehicleId, $ownerId) {
        $vehicle = $this->vehicleModel->getById($vehicleId);
        
        if ($vehicle && $vehicle['owner_id'] == $ownerId) {
            return $this->vehicleModel->delete($vehicleId);
        }
        return false;
    }
    
    // View my vehicles
    public function myVehicles($ownerId) {
        return $this->vehicleModel->getByOwner($ownerId);
    }
    
    // Create route
    public function createRoute($ownerId, $routeData) {
        // Verify vehicle belongs to owner
        $vehicle = $this->vehicleModel->getById($routeData['vehicle_id']);
        
        if ($vehicle && $vehicle['owner_id'] == $ownerId) {
            $routeData['owner_id'] = $ownerId;
            $routeData['created_at'] = date('Y-m-d H:i:s');
            
            return $this->routeModel->create($routeData);
        }
        return false;
    }
    
    // Update route
    public function updateRoute($routeId, $ownerId, $routeData) {
        $route = $this->routeModel->getById($routeId);
        
        if ($route && $route['owner_id'] == $ownerId) {
            return $this->routeModel->update($routeId, $routeData);
        }
        return false;
    }
    
    // View my routes
    public function myRoutes($ownerId) {
        return $this->routeModel->getByVehicleOwner($ownerId);
    }
    
    // View bookings
    public function viewBookings($ownerId, $status = 'all') {
        return $this->bookingModel->getVehicleBookingsByOwner($ownerId, $status);
    }
    
    // Update vehicle availability
    public function updateVehicleStatus($vehicleId, $ownerId, $status) {
        $vehicle = $this->vehicleModel->getById($vehicleId);
        
        if ($vehicle && $vehicle['owner_id'] == $ownerId) {
            return $this->vehicleModel->updateStatus($vehicleId, $status);
        }
        return false;
    }
    
    // Calculate earnings
    private function calculateEarnings($ownerId) {
        return $this->bookingModel->calculateVehicleEarnings($ownerId);
    }
    
    // Get statistics
    public function getStatistics($ownerId) {
        return [
            'total_vehicles' => $this->vehicleModel->countByOwner($ownerId),
            'active_routes' => $this->routeModel->countActiveRoutes($ownerId),
            'total_bookings' => $this->bookingModel->countVehicleBookings($ownerId),
            'this_month_earnings' => $this->bookingModel->getMonthlyVehicleEarnings($ownerId)
        ];
    }
}

?>

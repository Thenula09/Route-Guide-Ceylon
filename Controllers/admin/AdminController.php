<?php

class AdminController {
    
    private $userModel;
    private $routeModel;
    private $bookingModel;
    
    public function __construct() {
        // Initialize models
        require_once '../../Models/common/User.php';
        require_once '../../Models/common/Route.php';
        require_once '../../Models/common/Booking.php';
        
        $this->userModel = new User();
        $this->routeModel = new Route();
        $this->bookingModel = new Booking();
    }
    
    // Display admin dashboard
    public function dashboard() {
        $data = [
            'totalUsers' => $this->userModel->getTotalUsers(),
            'activeRoutes' => $this->routeModel->getActiveRoutes(),
            'totalHotels' => $this->userModel->getTotalHotels(),
            'totalVehicles' => $this->userModel->getTotalVehicles()
        ];
        
        include '../../Views/admin/admin.html';
    }
    
    // Manage users
    public function manageUsers() {
        $users = $this->userModel->getAllUsers();
        return $users;
    }
    
    // Add new user
    public function addUser($userData) {
        return $this->userModel->create($userData);
    }
    
    // Update user
    public function updateUser($userId, $userData) {
        return $this->userModel->update($userId, $userData);
    }
    
    // Delete user
    public function deleteUser($userId) {
        return $this->userModel->delete($userId);
    }
    
    // Manage routes
    public function manageRoutes() {
        $routes = $this->routeModel->getAllRoutes();
        return $routes;
    }
    
    // Generate reports
    public function generateReports($type) {
        switch($type) {
            case 'users':
                return $this->userModel->getUserReport();
            case 'bookings':
                return $this->bookingModel->getBookingReport();
            case 'revenue':
                return $this->bookingModel->getRevenueReport();
            default:
                return null;
        }
    }
    
    // Approve hotel/vehicle
    public function approveResource($type, $id) {
        if ($type === 'hotel') {
            require_once '../../Models/hotelOwner/Hotel.php';
            $hotel = new Hotel();
            return $hotel->approve($id);
        } elseif ($type === 'vehicle') {
            require_once '../../Models/vehicleOwner/Vehicle.php';
            $vehicle = new Vehicle();
            return $vehicle->approve($id);
        }
        return false;
    }
}

?>

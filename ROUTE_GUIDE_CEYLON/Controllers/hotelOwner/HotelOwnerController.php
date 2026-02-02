<?php

class HotelOwnerController {
    
    private $hotelModel;
    private $bookingModel;
    
    public function __construct() {
        require_once '../../Models/hotelOwner/Hotel.php';
        require_once '../../Models/common/Booking.php';
        
        $this->hotelModel = new Hotel();
        $this->bookingModel = new Booking();
    }
    
    // Display hotel owner dashboard
    public function dashboard($ownerId) {
        $data = [
            'properties' => $this->hotelModel->getByOwner($ownerId),
            'recentBookings' => $this->bookingModel->getHotelBookings($ownerId),
            'earnings' => $this->calculateEarnings($ownerId),
            'statistics' => $this->getStatistics($ownerId)
        ];
        
        include '../../Views/hotelOwner/hotelOwner.html';
    }
    
    // Add new property
    public function addProperty($ownerId, $propertyData) {
        $propertyData['owner_id'] = $ownerId;
        $propertyData['status'] = 'pending'; // Pending admin approval
        $propertyData['created_at'] = date('Y-m-d H:i:s');
        
        return $this->hotelModel->create($propertyData);
    }
    
    // Update property
    public function updateProperty($propertyId, $ownerId, $propertyData) {
        $property = $this->hotelModel->getById($propertyId);
        
        if ($property && $property['owner_id'] == $ownerId) {
            return $this->hotelModel->update($propertyId, $propertyData);
        }
        return false;
    }
    
    // Delete property
    public function deleteProperty($propertyId, $ownerId) {
        $property = $this->hotelModel->getById($propertyId);
        
        if ($property && $property['owner_id'] == $ownerId) {
            return $this->hotelModel->delete($propertyId);
        }
        return false;
    }
    
    // View my properties
    public function myProperties($ownerId) {
        return $this->hotelModel->getByOwner($ownerId);
    }
    
    // View bookings
    public function viewBookings($ownerId, $status = 'all') {
        return $this->bookingModel->getHotelBookingsByOwner($ownerId, $status);
    }
    
    // Update booking status
    public function updateBookingStatus($bookingId, $ownerId, $status) {
        $booking = $this->bookingModel->getById($bookingId);
        $hotel = $this->hotelModel->getById($booking['hotel_id']);
        
        if ($hotel && $hotel['owner_id'] == $ownerId) {
            return $this->bookingModel->updateStatus($bookingId, $status);
        }
        return false;
    }
    
    // Calculate earnings
    private function calculateEarnings($ownerId) {
        return $this->bookingModel->calculateHotelEarnings($ownerId);
    }
    
    // Get statistics
    private function getStatistics($ownerId) {
        return [
            'total_properties' => $this->hotelModel->countByOwner($ownerId),
            'total_bookings' => $this->bookingModel->countHotelBookings($ownerId),
            'pending_bookings' => $this->bookingModel->countPendingHotelBookings($ownerId),
            'this_month_earnings' => $this->bookingModel->getMonthlyEarnings($ownerId)
        ];
    }
    
    // Manage room availability
    public function updateAvailability($propertyId, $ownerId, $availabilityData) {
        $property = $this->hotelModel->getById($propertyId);
        
        if ($property && $property['owner_id'] == $ownerId) {
            return $this->hotelModel->updateAvailability($propertyId, $availabilityData);
        }
        return false;
    }
}

?>

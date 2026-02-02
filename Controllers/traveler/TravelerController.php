<?php

class TravelerController {
    
    private $routeModel;
    private $bookingModel;
    private $hotelModel;
    
    public function __construct() {
        require_once '../../Models/common/Route.php';
        require_once '../../Models/common/Booking.php';
        require_once '../../Models/hotelOwner/Hotel.php';
        
        $this->routeModel = new Route();
        $this->bookingModel = new Booking();
        $this->hotelModel = new Hotel();
    }
    
    // Display traveler dashboard
    public function dashboard($travelerId) {
        $data = [
            'upcomingBookings' => $this->bookingModel->getUpcomingBookings($travelerId),
            'featuredRoutes' => $this->routeModel->getFeaturedRoutes(),
            'recentSearches' => $this->getRecentSearches($travelerId)
        ];
        
        include '../../Views/traveler/traveler.html';
    }
    
    // Search routes
    public function searchRoutes($from, $to, $date) {
        $routes = $this->routeModel->search($from, $to, $date);
        return $routes;
    }
    
    // Book a trip
    public function bookTrip($travelerId, $routeId, $bookingData) {
        $bookingData['traveler_id'] = $travelerId;
        $bookingData['route_id'] = $routeId;
        $bookingData['status'] = 'pending';
        $bookingData['booking_date'] = date('Y-m-d H:i:s');
        
        return $this->bookingModel->create($bookingData);
    }
    
    // View my bookings
    public function myBookings($travelerId) {
        return $this->bookingModel->getUserBookings($travelerId);
    }
    
    // Cancel booking
    public function cancelBooking($bookingId, $travelerId) {
        $booking = $this->bookingModel->getById($bookingId);
        
        if ($booking && $booking['traveler_id'] == $travelerId) {
            return $this->bookingModel->cancel($bookingId);
        }
        return false;
    }
    
    // Search hotels
    public function searchHotels($location, $checkIn, $checkOut) {
        return $this->hotelModel->search($location, $checkIn, $checkOut);
    }
    
    // Book hotel
    public function bookHotel($travelerId, $hotelId, $bookingData) {
        $bookingData['traveler_id'] = $travelerId;
        $bookingData['hotel_id'] = $hotelId;
        $bookingData['status'] = 'pending';
        
        return $this->bookingModel->createHotelBooking($bookingData);
    }
    
    // Get recent searches
    private function getRecentSearches($travelerId) {
        // Implementation for getting user's recent searches
        return [];
    }
    
    // Rate and review
    public function submitReview($travelerId, $type, $id, $rating, $comment) {
        require_once '../../Models/common/Review.php';
        $review = new Review();
        
        $reviewData = [
            'traveler_id' => $travelerId,
            'type' => $type,
            'item_id' => $id,
            'rating' => $rating,
            'comment' => $comment,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $review->create($reviewData);
    }
}

?>

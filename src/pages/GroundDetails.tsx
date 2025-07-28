import { useState } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';
import { Calendar } from '@/components/ui/calendar';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { CalendarIcon, Clock, MapPin, Star, Users, Wifi, Car, Coffee, MessageSquare } from 'lucide-react';
import { format } from 'date-fns';
import { cn } from '@/lib/utils';
import GoogleMap from '@/components/GoogleMap';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Separator } from '@/components/ui/separator';
import sportsComplexImage from '@/assets/sports-complex.jpg';

const GroundDetails = () => {
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();
  const [selectedDate, setSelectedDate] = useState<Date>();
  const [selectedTime, setSelectedTime] = useState('');

  const groundId = searchParams.get('id') || '1';
  
  // Mock data - in a real app this would come from an API
  const ground = {
    id: 1,
    name: 'City Sports Complex',
    type: 'Multi-Sport Facility',
    sports: ['Football', 'Basketball', 'Tennis'],
    location: 'Downtown Area',
    address: '123 Sports Avenue, Downtown Area, City',
    coordinates: { lat: 40.7128, lng: -74.0060 },
    rating: 4.9,
    totalReviews: 156,
    price: 25,
    image: sportsComplexImage,
    amenities: ['Parking', 'Changing Rooms', 'Equipment Rental', 'Cafeteria', 'WiFi', 'AC'],
    description: 'Premium sports complex with state-of-the-art facilities and professional-grade equipment.',
    capacity: 50,
    images: [sportsComplexImage, sportsComplexImage, sportsComplexImage],
    reviews: [
      {
        id: 1,
        name: 'Alex Johnson',
        rating: 5,
        comment: 'Excellent facilities with top-notch equipment. The staff is very helpful and the booking process is seamless.',
        date: '2024-01-15',
        verified: true
      },
      {
        id: 2,
        name: 'Maria Santos',
        rating: 5,
        comment: 'Great location and amazing courts. Perfect for our weekly tennis sessions. Highly recommended!',
        date: '2024-01-12',
        verified: true
      },
      {
        id: 3,
        name: 'David Chen',
        rating: 4,
        comment: 'Good facilities overall. The only minor issue is parking can get crowded during peak hours.',
        date: '2024-01-08',
        verified: false
      },
      {
        id: 4,
        name: 'Sarah Wilson',
        rating: 5,
        comment: 'Perfect venue for our basketball team practice. Clean, well-maintained, and reasonably priced.',
        date: '2024-01-05',
        verified: true
      }
    ]
  };

  const timeSlots = ['08:00', '09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00'];
  
  // Mock booked slots - in a real app this would come from an API
  const bookedSlots = {
    [format(new Date(), 'yyyy-MM-dd')]: ['09:00', '15:00', '16:00'],
    [format(new Date(Date.now() + 86400000), 'yyyy-MM-dd')]: ['10:00', '11:00']
  };

  const getAvailableSlots = (date: Date) => {
    const dateKey = format(date, 'yyyy-MM-dd');
    const booked = bookedSlots[dateKey] || [];
    return timeSlots.filter(time => !booked.includes(time));
  };

  const handleBooking = () => {
    if (selectedDate && selectedTime) {
      navigate(`/payment?type=ground&id=${groundId}&date=${format(selectedDate, 'yyyy-MM-dd')}&time=${selectedTime}&price=${ground.price}`);
    }
  };

  const amenityIcons = {
    'Parking': Car,
    'WiFi': Wifi,
    'Cafeteria': Coffee,
    'Changing Rooms': Users,
    'Equipment Rental': Users,
    'AC': Users
  };

  return (
    <div className="min-h-screen bg-background">
      <div className="container mx-auto px-4 py-8">
        <Button 
          variant="outline" 
          onClick={() => navigate('/book-ground')}
          className="mb-6"
        >
          ← Back to Grounds
        </Button>

        <div className="grid lg:grid-cols-2 gap-8">
          {/* Ground Info */}
          <div>
            <div className="relative h-80 rounded-lg overflow-hidden mb-6">
              <img 
                src={ground.image} 
                alt={ground.name}
                className="w-full h-full object-cover"
              />
              <div className="absolute top-4 right-4 bg-white/90 backdrop-blur-sm text-foreground px-3 py-2 rounded-full flex items-center gap-1">
                <Star className="h-4 w-4 fill-yellow-400 text-yellow-400" />
                <span className="font-semibold">{ground.rating}</span>
                <span className="text-sm text-muted-foreground">({ground.totalReviews})</span>
              </div>
            </div>

            <h1 className="text-3xl font-bold text-foreground mb-2">{ground.name}</h1>
            <p className="text-muted-foreground text-lg mb-4">{ground.type}</p>
            
            <div className="flex items-center gap-2 mb-6">
              <MapPin className="h-5 w-5 text-muted-foreground" />
              <span className="text-muted-foreground">{ground.address}</span>
            </div>

            <p className="text-foreground mb-6">{ground.description}</p>

            <div className="mb-6">
              <h3 className="font-semibold text-foreground mb-3">Sports Available:</h3>
              <div className="flex flex-wrap gap-2">
                {ground.sports.map((sport) => (
                  <Badge key={sport} variant="secondary" className="bg-primary/10 text-primary">
                    {sport}
                  </Badge>
                ))}
              </div>
            </div>

            <div className="mb-6">
              <h3 className="font-semibold text-foreground mb-3">Amenities:</h3>
              <div className="grid grid-cols-2 gap-3">
                {ground.amenities.map((amenity) => {
                  const Icon = amenityIcons[amenity] || Users;
                  return (
                    <div key={amenity} className="flex items-center gap-2">
                      <Icon className="h-4 w-4 text-primary" />
                      <span className="text-sm">{amenity}</span>
                    </div>
                  );
                })}
              </div>
            </div>

            {/* Location Map */}
            <div className="mb-6">
              <h3 className="font-semibold text-foreground mb-3">Location & Map:</h3>
              <GoogleMap 
                address={ground.address}
                lat={ground.coordinates.lat}
                lng={ground.coordinates.lng}
                height="300px"
                className="rounded-lg"
              />
            </div>

            {/* Reviews Section */}
            <div className="mb-6">
              <div className="flex items-center justify-between mb-4">
                <h3 className="font-semibold text-foreground">Reviews & Ratings</h3>
                <div className="flex items-center gap-2">
                  <Star className="h-5 w-5 fill-yellow-400 text-yellow-400" />
                  <span className="font-semibold">{ground.rating}</span>
                  <span className="text-muted-foreground">({ground.totalReviews} reviews)</span>
                </div>
              </div>
              
              <div className="space-y-4">
                {ground.reviews.slice(0, 3).map((review) => (
                  <div key={review.id} className="border border-border/20 rounded-lg p-4">
                    <div className="flex items-start justify-between mb-3">
                      <div className="flex items-center gap-3">
                        <Avatar className="h-10 w-10">
                          <AvatarFallback>{review.name.charAt(0)}</AvatarFallback>
                        </Avatar>
                        <div>
                          <div className="flex items-center gap-2">
                            <p className="font-medium text-foreground">{review.name}</p>
                            {review.verified && (
                              <Badge variant="outline" className="text-xs bg-green-50 text-green-700 border-green-200">
                                Verified
                              </Badge>
                            )}
                          </div>
                          <div className="flex items-center gap-1 mt-1">
                            {Array.from({ length: review.rating }).map((_, i) => (
                              <Star key={i} className="h-3 w-3 fill-yellow-400 text-yellow-400" />
                            ))}
                          </div>
                        </div>
                      </div>
                      <span className="text-xs text-muted-foreground">
                        {new Date(review.date).toLocaleDateString()}
                      </span>
                    </div>
                    <p className="text-muted-foreground text-sm italic">"{review.comment}"</p>
                  </div>
                ))}
                
                {ground.reviews.length > 3 && (
                  <Button variant="outline" className="w-full">
                    <MessageSquare className="mr-2 h-4 w-4" />
                    View All {ground.totalReviews} Reviews
                  </Button>
                )}
              </div>
            </div>
          </div>

          {/* Booking Section */}
          <div>
            <Card className="sticky top-8">
              <CardHeader>
                <CardTitle>Book This Ground</CardTitle>
                <div className="text-2xl font-bold text-primary">
                  ${ground.price}<span className="text-sm text-muted-foreground">/hour</span>
                </div>
              </CardHeader>
              <CardContent className="space-y-6">
                <div>
                  <label className="block text-sm font-medium text-card-foreground mb-2">
                    Select Date
                  </label>
                  <Popover>
                    <PopoverTrigger asChild>
                      <Button
                        variant="outline"
                        className={cn(
                          "w-full justify-start text-left font-normal",
                          !selectedDate && "text-muted-foreground"
                        )}
                      >
                        <CalendarIcon className="mr-2 h-4 w-4" />
                        {selectedDate ? format(selectedDate, "PPP") : <span>Pick a date</span>}
                      </Button>
                    </PopoverTrigger>
                    <PopoverContent className="w-auto p-0" align="start">
                      <Calendar
                        mode="single"
                        selected={selectedDate}
                        onSelect={setSelectedDate}
                        disabled={(date) => date < new Date()}
                        initialFocus
                        className="pointer-events-auto"
                      />
                    </PopoverContent>
                  </Popover>
                </div>

                {selectedDate && (
                  <div>
                    <label className="block text-sm font-medium text-card-foreground mb-2">
                      Available Times
                    </label>
                    <div className="grid grid-cols-3 gap-2">
                      {getAvailableSlots(selectedDate).map((time) => (
                        <Button
                          key={time}
                          variant={selectedTime === time ? "default" : "outline"}
                          size="sm"
                          onClick={() => setSelectedTime(time)}
                          className="text-xs"
                        >
                          {time}
                        </Button>
                      ))}
                    </div>
                    {getAvailableSlots(selectedDate).length === 0 && (
                      <p className="text-sm text-muted-foreground">No available slots for this date</p>
                    )}
                  </div>
                )}

                {selectedDate && selectedTime && (
                  <div className="bg-muted/50 p-4 rounded-lg">
                    <h4 className="font-medium mb-2">Booking Summary</h4>
                    <div className="space-y-1 text-sm">
                      <div className="flex justify-between">
                        <span>Date:</span>
                        <span>{format(selectedDate, "PPP")}</span>
                      </div>
                      <div className="flex justify-between">
                        <span>Time:</span>
                        <span>{selectedTime}</span>
                      </div>
                      <div className="flex justify-between font-medium">
                        <span>Total:</span>
                        <span>${ground.price}</span>
                      </div>
                    </div>
                  </div>
                )}

                <Button 
                  className="w-full bg-gradient-primary"
                  onClick={handleBooking}
                  disabled={!selectedDate || !selectedTime}
                >
                  <Clock className="mr-2 h-4 w-4" />
                  Book Now
                </Button>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  );
};

export default GroundDetails;
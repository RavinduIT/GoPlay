import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Calendar, Clock, MapPin, Star, Filter, Search } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import bookingBackground from '@/assets/booking-background.jpg';
import sportsComplexImage from '@/assets/sports-complex.jpg';
import tennisCourtImage from '@/assets/tennis-court.jpg';
import basketballCourtImage from '@/assets/basketball-court.jpg';

const BookGround = () => {
  const navigate = useNavigate();
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedLocation, setSelectedLocation] = useState('');
  const [selectedSport, setSelectedSport] = useState('');

  const sports = ['Football', 'Basketball', 'Tennis', 'Volleyball', 'Badminton'];
  const locations = ['Downtown Area', 'Sports District', 'North Side', 'Valley Area', 'East Zone', 'West Side'];
  const timeSlots = ['08:00', '09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00'];

  const grounds = [
    {
      id: 1,
      name: 'City Sports Complex',
      type: 'Multi-Sport Facility',
      sports: ['Football', 'Basketball', 'Tennis'],
      location: 'Downtown Area',
      rating: 4.9,
      price: 25,
      image: sportsComplexImage,
      amenities: ['Parking', 'Changing Rooms', 'Equipment Rental', 'Cafeteria'],
      availability: ['08:00', '09:00', '14:00', '15:00', '16:00']
    },
    {
      id: 2,
      name: 'Premier Tennis Courts',
      type: 'Tennis Facility',
      sports: ['Tennis'],
      location: 'Sports District',
      rating: 4.8,
      price: 30,
      image: tennisCourtImage,
      amenities: ['Parking', 'Pro Shop', 'Coaching', 'Ball Machine'],
      availability: ['09:00', '10:00', '11:00', '15:00', '16:00', '17:00']
    },
    {
      id: 3,
      name: 'Basketball Arena Pro',
      type: 'Basketball Courts',
      sports: ['Basketball'],
      location: 'North Side',
      rating: 4.7,
      price: 20,
      image: basketballCourtImage,
      amenities: ['Parking', 'Changing Rooms', 'Sound System', 'Scoreboard'],
      availability: ['08:00', '10:00', '14:00', '18:00', '19:00']
    },
    {
      id: 4,
      name: 'Green Valley Football Ground',
      type: 'Football Field',
      sports: ['Football'],
      location: 'Valley Area',
      rating: 4.6,
      price: 35,
      image: sportsComplexImage,
      amenities: ['Parking', 'Floodlights', 'Changing Rooms', 'Goal Posts'],
      availability: ['08:00', '09:00', '15:00', '16:00', '17:00', '18:00']
    }
  ];

  const filteredGrounds = grounds.filter(ground => {
    if (searchQuery && !ground.name.toLowerCase().includes(searchQuery.toLowerCase())) return false;
    if (selectedSport && selectedSport !== 'all-sports' && !ground.sports.includes(selectedSport)) return false;
    if (selectedLocation && selectedLocation !== 'all-locations' && ground.location !== selectedLocation) return false;
    return true;
  });

  return (
    <div className="min-h-screen bg-background">
      {/* Header Section */}
      <div 
        className="relative h-64 bg-cover bg-center flex items-center justify-center"
        style={{ backgroundImage: `url(${bookingBackground})` }}
      >
        <div className="absolute inset-0 bg-black/60"></div>
        <div className="relative text-center text-white">
          <h1 className="text-4xl md:text-5xl font-bold mb-4">
            Book Sports Grounds
          </h1>
          <p className="text-xl opacity-90 max-w-2xl mx-auto">
            Find and reserve the perfect sports facility for your game
          </p>
        </div>
      </div>

      {/* Search & Filter Section */}
      <div className="container mx-auto px-4 -mt-8 relative z-10">
        <Card className="shadow-strong bg-card/95 backdrop-blur-sm">
          <CardContent className="p-6">
            <div className="grid md:grid-cols-3 gap-4 mb-4">
              <div>
                <label className="block text-sm font-medium text-card-foreground mb-2">
                  Search Grounds
                </label>
                <div className="flex gap-2">
                  <Input
                    type="text"
                    placeholder="Search by ground name..."
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                    className="flex-1"
                  />
                  <Button className="bg-gradient-primary">
                    <Search className="h-4 w-4" />
                  </Button>
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-card-foreground mb-2">
                  Select Location
                </label>
                <Select value={selectedLocation} onValueChange={setSelectedLocation}>
                  <SelectTrigger>
                    <SelectValue placeholder="All locations" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all-locations">All Locations</SelectItem>
                    {locations.map((location) => (
                      <SelectItem key={location} value={location}>
                        {location}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
              <div>
                <label className="block text-sm font-medium text-card-foreground mb-2">
                  Sport Type
                </label>
                <Select value={selectedSport} onValueChange={setSelectedSport}>
                  <SelectTrigger>
                    <SelectValue placeholder="All sports" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all-sports">All Sports</SelectItem>
                    {sports.map((sport) => (
                      <SelectItem key={sport} value={sport}>
                        {sport}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Results Section */}
      <div className="container mx-auto px-4 py-12">
        <div className="flex items-center justify-between mb-8">
          <h2 className="text-2xl font-bold text-foreground">
            Available Grounds ({filteredGrounds.length})
          </h2>
          <Button variant="outline" size="sm">
            <Filter className="mr-2 h-4 w-4" />
            Filters
          </Button>
        </div>

        <div className="grid lg:grid-cols-2 gap-8">
          {filteredGrounds.map((ground) => (
            <Card key={ground.id} className="hover:shadow-medium transition-all duration-300 hover:-translate-y-1">
              <CardHeader className="pb-4">
                <div className="flex items-start justify-between">
                  <div className="flex items-center gap-4">
                    <img 
                      src={ground.image} 
                      alt={ground.name}
                      className="w-16 h-16 object-cover rounded-lg"
                    />
                    <div>
                      <CardTitle className="text-xl text-card-foreground mb-1">
                        {ground.name}
                      </CardTitle>
                      <p className="text-muted-foreground">{ground.type}</p>
                      <div className="flex items-center gap-1 mt-1">
                        <MapPin className="h-4 w-4 text-muted-foreground" />
                        <span className="text-sm text-muted-foreground">{ground.location}</span>
                      </div>
                    </div>
                  </div>
                  <div className="text-right">
                    <div className="flex items-center gap-1 mb-1">
                      <Star className="h-4 w-4 fill-yellow-400 text-yellow-400" />
                      <span className="font-medium">{ground.rating}</span>
                    </div>
                    <div className="text-2xl font-bold text-primary">
                      ${ground.price}<span className="text-sm text-muted-foreground">/hour</span>
                    </div>
                  </div>
                </div>
              </CardHeader>
              <CardContent>
                <div className="mb-4">
                  <h4 className="font-medium text-card-foreground mb-2">Sports Available:</h4>
                  <div className="flex flex-wrap gap-2">
                    {ground.sports.map((sport) => (
                      <Badge key={sport} variant="secondary" className="bg-primary/10 text-primary">
                        {sport}
                      </Badge>
                    ))}
                  </div>
                </div>

                <div className="mb-4">
                  <h4 className="font-medium text-card-foreground mb-2">Amenities:</h4>
                  <div className="flex flex-wrap gap-2">
                    {ground.amenities.map((amenity) => (
                      <Badge key={amenity} variant="outline" className="text-xs">
                        {amenity}
                      </Badge>
                    ))}
                  </div>
                </div>

                <div className="mb-6">
                  <h4 className="font-medium text-card-foreground mb-2">Available Times Today:</h4>
                  <div className="grid grid-cols-5 gap-2">
                    {ground.availability.map((time) => (
                      <Button
                        key={time}
                        variant="outline"
                        size="sm"
                        className="text-xs hover:bg-primary hover:text-primary-foreground"
                      >
                        {time}
                      </Button>
                    ))}
                  </div>
                </div>

                <div className="flex gap-3">
                  <Button 
                    variant="outline" 
                    className="flex-1"
                    onClick={() => navigate(`/ground-details?id=${ground.id}`)}
                  >
                    <Calendar className="mr-2 h-4 w-4" />
                    View Details
                  </Button>
                  <Button 
                    className="flex-1 bg-gradient-primary"
                    onClick={() => navigate(`/ground-details?id=${ground.id}`)}
                  >
                    Book Now
                  </Button>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      </div>
    </div>
  );
};

export default BookGround;
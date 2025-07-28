import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Star, MapPin, Clock, Award, Filter, Search, User } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import coachBackground from '@/assets/coach-background.jpg';
import coachTrainingImage from '@/assets/coach-training.jpg';
import coachCertification from '@/assets/coach-certification.jpg';

const BookCoach = () => {
  const navigate = useNavigate();
  const [selectedSport, setSelectedSport] = useState('');
  const [selectedLocation, setSelectedLocation] = useState('');
  const [priceRange, setPriceRange] = useState('');

  const sports = ['Football', 'Basketball', 'Tennis', 'Swimming', 'Volleyball', 'Badminton'];
  const locations = ['Downtown', 'Sports District', 'North Side', 'Valley Area', 'East End'];
  const priceRanges = ['$20-40/hour', '$40-60/hour', '$60-80/hour', '$80+/hour'];

  const coaches = [
    {
      id: 1,
      name: 'John Martinez',
      sport: 'Football',
      experience: 8,
      rating: 4.9,
      reviews: 156,
      price: 45,
      location: 'Downtown',
      avatar: coachTrainingImage,
      specializations: ['Youth Training', 'Professional Coaching', 'Tactical Analysis'],
      certifications: ['UEFA B License', 'Sports Science Diploma'],
      availability: ['Mon', 'Wed', 'Fri', 'Sat'],
      languages: ['English', 'Spanish'],
      bio: 'Former professional player with 8 years coaching experience. Specialized in youth development.'
    },
    {
      id: 2,
      name: 'Sarah Thompson',
      sport: 'Tennis',
      experience: 12,
      rating: 4.8,
      reviews: 203,
      price: 60,
      location: 'Sports District',
      avatar: coachTrainingImage,
      specializations: ['Tournament Preparation', 'Technique Development', 'Mental Training'],
      certifications: ['PTR Professional', 'Sports Psychology'],
      availability: ['Tue', 'Thu', 'Fri', 'Sun'],
      languages: ['English', 'French'],
      bio: 'Professional tennis coach with tournament experience. Focus on competitive players.'
    },
    {
      id: 3,
      name: 'Mike Johnson',
      sport: 'Basketball',
      experience: 6,
      rating: 4.7,
      reviews: 89,
      price: 40,
      location: 'North Side',
      avatar: coachTrainingImage,
      specializations: ['Shooting Technique', 'Defense Training', 'Team Strategy'],
      certifications: ['NBA Coach Certification', 'Basketball Fundamentals'],
      availability: ['Mon', 'Tue', 'Thu', 'Sat'],
      languages: ['English'],
      bio: 'Former college player focused on fundamentals and skill development for all levels.'
    },
    {
      id: 4,
      name: 'Lisa Chen',
      sport: 'Swimming',
      experience: 10,
      rating: 4.9,
      reviews: 134,
      price: 55,
      location: 'Valley Area',
      avatar: coachTrainingImage,
      specializations: ['Stroke Technique', 'Competitive Swimming', 'Water Safety'],
      certifications: ['Swim England Level 3', 'Lifeguard Certified'],
      availability: ['Wed', 'Thu', 'Fri', 'Sun'],
      languages: ['English', 'Mandarin'],
      bio: 'Olympic-level training experience with focus on technique and competitive preparation.'
    }
  ];

  const filteredCoaches = coaches.filter(coach => {
    if (selectedSport && selectedSport !== 'all-sports' && coach.sport !== selectedSport) return false;
    if (selectedLocation && selectedLocation !== 'all-locations' && coach.location !== selectedLocation) return false;
    return true;
  });

  return (
    <div className="min-h-screen bg-background">
      {/* Header Section */}
      <div className="bg-gradient-hero py-16 relative overflow-hidden">
        <div 
          className="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20"
          style={{ backgroundImage: `url(${coachBackground})` }}
        />
        <div className="container mx-auto px-4 text-center relative z-10">
          <h1 className="text-4xl md:text-5xl font-bold text-primary-foreground mb-4">
            Book Professional Coaches
          </h1>
          <p className="text-xl text-primary-foreground/90 max-w-2xl mx-auto">
            Train with certified coaches and take your skills to the next level
          </p>
        </div>
      </div>

      {/* Search & Filter Section */}
      <div className="container mx-auto px-4 -mt-8 relative z-10">
        <Card className="shadow-strong bg-card/95 backdrop-blur-sm">
          <CardContent className="p-6">
            <div className="grid md:grid-cols-4 gap-4">
              <div>
                <label className="block text-sm font-medium text-card-foreground mb-2">
                  Sport
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
              <div>
                <label className="block text-sm font-medium text-card-foreground mb-2">
                  Location
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
                  Price Range
                </label>
                <Select value={priceRange} onValueChange={setPriceRange}>
                  <SelectTrigger>
                    <SelectValue placeholder="Any price" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="any-price">Any Price</SelectItem>
                    {priceRanges.map((range) => (
                      <SelectItem key={range} value={range}>
                        {range}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
              <div className="flex items-end">
                <Button className="w-full bg-gradient-primary">
                  <Search className="mr-2 h-4 w-4" />
                  Search
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Results Section */}
      <div className="container mx-auto px-4 py-12">
        <div className="flex items-center justify-between mb-8">
          <h2 className="text-2xl font-bold text-foreground">
            Available Coaches ({filteredCoaches.length})
          </h2>
          <Button variant="outline" size="sm">
            <Filter className="mr-2 h-4 w-4" />
            More Filters
          </Button>
        </div>

        <div className="grid lg:grid-cols-2 gap-8">
          {filteredCoaches.map((coach) => (
            <Card key={coach.id} className="hover:shadow-medium transition-all duration-300 hover:-translate-y-1">
              <CardHeader className="pb-4">
                <div className="flex items-start gap-4">
                  <Avatar className="h-16 w-16">
                    <AvatarImage src={coach.avatar} alt={coach.name} />
                    <AvatarFallback className="text-2xl">{coach.name.charAt(0)}</AvatarFallback>
                  </Avatar>
                  <div className="flex-1">
                    <div className="flex items-start justify-between">
                      <div>
                        <CardTitle className="text-xl text-card-foreground mb-1">
                          {coach.name}
                        </CardTitle>
                        <Badge variant="secondary" className="bg-primary/10 text-primary mb-2">
                          {coach.sport} Coach
                        </Badge>
                        <div className="flex items-center gap-4 text-sm text-muted-foreground">
                          <div className="flex items-center gap-1">
                            <Award className="h-4 w-4" />
                            <span>{coach.experience} years exp.</span>
                          </div>
                          <div className="flex items-center gap-1">
                            <MapPin className="h-4 w-4" />
                            <span>{coach.location}</span>
                          </div>
                        </div>
                      </div>
                      <div className="text-right">
                        <div className="flex items-center gap-1 mb-1">
                          <Star className="h-4 w-4 fill-yellow-400 text-yellow-400" />
                          <span className="font-medium">{coach.rating}</span>
                          <span className="text-sm text-muted-foreground">({coach.reviews})</span>
                        </div>
                        <div className="text-2xl font-bold text-primary">
                          ${coach.price}<span className="text-sm text-muted-foreground">/hour</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </CardHeader>
              <CardContent>
                <p className="text-muted-foreground mb-4 text-sm">
                  {coach.bio}
                </p>

                <div className="mb-4">
                  <h4 className="font-medium text-card-foreground mb-2">Specializations:</h4>
                  <div className="flex flex-wrap gap-2">
                    {coach.specializations.map((spec) => (
                      <Badge key={spec} variant="outline" className="text-xs">
                        {spec}
                      </Badge>
                    ))}
                  </div>
                </div>

                <div className="mb-4">
                  <h4 className="font-medium text-card-foreground mb-2">Certifications:</h4>
                  <div className="flex flex-wrap gap-2">
                    {coach.certifications.map((cert) => (
                      <Badge key={cert} variant="secondary" className="text-xs bg-accent/10 text-accent">
                        {cert}
                      </Badge>
                    ))}
                  </div>
                </div>

                <div className="mb-6">
                  <h4 className="font-medium text-card-foreground mb-2">Location:</h4>
                  <div className="flex items-center gap-2">
                    <MapPin className="h-4 w-4 text-muted-foreground" />
                    <Badge variant="outline" className="text-sm">
                      {coach.location}
                    </Badge>
                  </div>
                </div>

                <Button 
                  className="w-full bg-gradient-primary"
                  onClick={() => navigate(`/coach-profile?id=${coach.id}`)}
                >
                  <Clock className="mr-2 h-4 w-4" />
                  Book Coach
                </Button>
              </CardContent>
            </Card>
          ))}
        </div>
      </div>
    </div>
  );
};

export default BookCoach;
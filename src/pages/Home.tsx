import { Link } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Calendar, User, ShoppingCart, MapPin, Star, Clock } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import NewsCarousel from '@/components/NewsCarousel';
import heroBackground from '@/assets/hero-background.jpg';
import sportsComplexImage from '@/assets/sports-complex.jpg';
import tennisCourtImage from '@/assets/tennis-court.jpg';
import basketballCourtImage from '@/assets/basketball-court.jpg';
import featureBookingImage from '@/assets/feature-booking.jpg';
import featureCoachingImage from '@/assets/feature-coaching.jpg';
import featureEquipmentImage from '@/assets/feature-equipment.jpg';

const Home = () => {
  const features = [
    {
      icon: Calendar,
      title: 'Book Sports Grounds',
      description: 'Reserve courts, fields, and complexes for your games',
      link: '/book-ground',
      color: 'text-primary',
      image: featureBookingImage,
      stats: '500+ Venues'
    },
    {
      icon: User,
      title: 'Hire Professional Coaches',
      description: 'Train with certified coaches for all sports',
      link: '/book-coach',
      color: 'text-accent',
      image: featureCoachingImage,
      stats: '200+ Coaches'
    },
    {
      icon: ShoppingCart,
      title: 'Sports Equipment Shop',
      description: 'Buy quality gear from trusted brands',
      link: '/shop',
      color: 'text-primary',
      image: featureEquipmentImage,
      stats: '1000+ Products'
    }
  ];

  const popularVenues = [
    {
      name: 'City Sports Complex',
      type: 'Multi-Sport',
      rating: 4.9,
      price: '$25/hour',
      image: sportsComplexImage
    },
    {
      name: 'Tennis Center Pro',
      type: 'Tennis Courts',
      rating: 4.8,
      price: '$30/hour',
      image: tennisCourtImage
    },
    {
      name: 'Basketball Arena',
      type: 'Basketball',
      rating: 4.7,
      price: '$20/hour',
      image: basketballCourtImage
    }
  ];

  return (
    <div className="min-h-screen">
      {/* Hero Section */}
      <section className="relative h-[600px] bg-gradient-hero flex items-center justify-center overflow-hidden">
        <div 
          className="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-30"
          style={{ backgroundImage: `url(${heroBackground})` }}
        />
        <div className="relative container mx-auto px-4 text-center text-primary-foreground">
          <h1 className="text-5xl md:text-6xl font-bold mb-6 animate-fade-in">
            Your Ultimate <span className="text-primary-glow">Sports Hub</span>
          </h1>
          <p className="text-xl md:text-2xl mb-8 max-w-3xl mx-auto opacity-90">
            Book grounds, hire coaches, buy equipment, and stay updated with the latest sports news
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Button asChild size="lg" className="bg-primary-foreground text-primary hover:bg-primary-foreground/90 shadow-strong">
              <Link to="/book-ground">
                <Calendar className="mr-2 h-5 w-5" />
                Book Now
              </Link>
            </Button>
            <Button asChild size="lg" variant="outline" className="border-primary-foreground text-primary-foreground hover:bg-primary-foreground/10">
              <Link to="/shop">
                Explore Sports
              </Link>
            </Button>
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section className="py-20 bg-gradient-to-b from-background to-muted/30 relative overflow-hidden">
        <div className="absolute inset-0 bg-primary/5 opacity-40"></div>
        <div className="container mx-auto px-4 relative">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-foreground mb-6 animate-fade-in">
              Everything You Need for <span className="bg-gradient-primary bg-clip-text text-transparent">Sports</span>
            </h2>
            <p className="text-muted-foreground text-xl max-w-3xl mx-auto leading-relaxed">
              From booking world-class facilities to finding expert coaches and premium equipment
            </p>
          </div>

          <div className="grid md:grid-cols-3 gap-8">
            {features.map((feature, index) => (
              <Card key={index} className="group relative overflow-hidden bg-gradient-to-br from-card to-card/80 border-0 shadow-soft hover:shadow-strong transition-all duration-500 hover:-translate-y-2">
                <div className="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div className="relative h-48 overflow-hidden">
                  <img 
                    src={feature.image} 
                    alt={feature.title}
                    className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                  <div className="absolute top-4 right-4 bg-primary/90 text-primary-foreground px-3 py-1 rounded-full text-sm font-semibold">
                    {feature.stats}
                  </div>
                </div>
                <CardHeader className="text-center pb-4 relative">
                  <div className={`w-16 h-16 mx-auto bg-gradient-primary rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-medium`}>
                    <feature.icon className="h-8 w-8 text-primary-foreground" />
                  </div>
                  <CardTitle className="text-2xl font-bold text-card-foreground group-hover:text-primary transition-colors duration-300">
                    {feature.title}
                  </CardTitle>
                </CardHeader>
                <CardContent className="text-center relative">
                  <p className="text-muted-foreground mb-6 text-lg leading-relaxed">
                    {feature.description}
                  </p>
                  <Button asChild className="w-full bg-gradient-primary hover:shadow-medium transition-all duration-300 text-lg py-6">
                    <Link to={feature.link}>
                      Get Started
                    </Link>
                  </Button>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      </section>

      {/* News Section */}
      <section className="py-20 relative overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-br from-muted/50 to-accent/10"></div>
        <div className="absolute inset-0 bg-accent/5 opacity-30"></div>
        <div className="container mx-auto px-4 relative">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-foreground mb-6">
              Latest <span className="bg-gradient-primary bg-clip-text text-transparent">Sports News</span>
            </h2>
            <p className="text-muted-foreground text-xl max-w-2xl mx-auto">
              Stay updated with the latest happenings in the sports world
            </p>
          </div>
          <div className="bg-card/80 backdrop-blur-sm rounded-3xl p-8 shadow-strong border border-border/20">
            <NewsCarousel />
          </div>
        </div>
      </section>

      {/* Popular Venues */}
      <section className="py-20 bg-gradient-to-t from-background to-muted/20">
        <div className="container mx-auto px-4">
          <div className="text-center mb-16">
            <h2 className="text-4xl md:text-5xl font-bold text-foreground mb-6">
              Popular <span className="bg-gradient-primary bg-clip-text text-transparent">Venues</span>
            </h2>
            <p className="text-muted-foreground text-xl max-w-2xl mx-auto">
              Book your favorite sports facilities at premium locations
            </p>
          </div>

          <div className="grid md:grid-cols-3 gap-8">
            {popularVenues.map((venue, index) => (
              <Card key={index} className="group hover:shadow-strong transition-all duration-500 hover:-translate-y-3 overflow-hidden border-0 bg-gradient-to-br from-card to-card/90">
                <div className="relative h-64 overflow-hidden">
                  <img 
                    src={venue.image} 
                    alt={venue.name}
                    className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent group-hover:from-black/70 transition-all duration-300" />
                  <div className="absolute top-4 right-4 bg-white/90 backdrop-blur-sm text-foreground px-3 py-1 rounded-full text-sm font-bold flex items-center gap-1">
                    <Star className="h-3 w-3 fill-yellow-400 text-yellow-400" />
                    {venue.rating}
                  </div>
                  <div className="absolute bottom-4 left-4 text-white">
                    <h3 className="text-2xl font-bold mb-1">{venue.name}</h3>
                    <p className="text-white/90 text-lg">{venue.type}</p>
                  </div>
                </div>
                <CardContent className="p-6">
                  <div className="flex items-center justify-between mb-6">
                    <div className="flex items-center gap-2 text-primary font-bold text-xl">
                      <Clock className="h-5 w-5" />
                      <span>{venue.price}</span>
                    </div>
                    <Badge variant="secondary" className="text-sm font-semibold px-3 py-1">
                      Available Now
                    </Badge>
                  </div>
                  <Button asChild className="w-full bg-gradient-primary hover:shadow-medium transition-all duration-300 text-lg py-6 group">
                    <Link to="/book-ground">
                      <MapPin className="mr-2 h-5 w-5 group-hover:animate-pulse" />
                      Book Now
                    </Link>
                  </Button>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
};

export default Home;
import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Clock, CheckCircle, XCircle, Calendar, MapPin, DollarSign } from 'lucide-react';
import { format } from 'date-fns';
import RatingFeedback from '@/components/RatingFeedback';

interface BookingRequest {
  id: string;
  coachId: string;
  coachName: string;
  coachAvatar: string;
  sport: string;
  date: string;
  time: string;
  venue: string;
  price: number;
  status: 'pending' | 'accepted' | 'declined' | 'completed';
  requestDate: string;
  sessionNotes?: string;
}

const MyBookings = () => {
  const navigate = useNavigate();
  const [bookings] = useState<BookingRequest[]>([
    {
      id: '1',
      coachId: '1',
      coachName: 'John Martinez',
      coachAvatar: '/api/placeholder/64/64',
      sport: 'Football',
      date: '2024-02-15',
      time: '10:00',
      venue: 'Central Park Sports Field, Main area near the tennis courts',
      price: 45,
      status: 'completed',
      requestDate: '2024-02-10',
      sessionNotes: 'Great session focused on dribbling techniques and ball control.'
    },
    {
      id: '2',
      coachId: '2',
      coachName: 'Sarah Thompson',
      coachAvatar: '/api/placeholder/64/64',
      sport: 'Tennis',
      date: '2024-02-20',
      time: '14:00',
      venue: 'Riverside Tennis Club, Court 3',
      price: 60,
      status: 'accepted',
      requestDate: '2024-02-12'
    },
    {
      id: '3',
      coachId: '3',
      coachName: 'Mike Johnson',
      coachAvatar: '/api/placeholder/64/64',
      sport: 'Basketball',
      date: '2024-02-25',
      time: '16:00',
      venue: 'Downtown Basketball Court',
      price: 40,
      status: 'pending',
      requestDate: '2024-02-14'
    }
  ]);

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'pending':
        return <Badge variant="outline" className="bg-yellow-50 text-yellow-700 border-yellow-200">
          <Clock className="h-3 w-3 mr-1" />
          Pending
        </Badge>;
      case 'accepted':
        return <Badge variant="outline" className="bg-green-50 text-green-700 border-green-200">
          <CheckCircle className="h-3 w-3 mr-1" />
          Accepted
        </Badge>;
      case 'declined':
        return <Badge variant="outline" className="bg-red-50 text-red-700 border-red-200">
          <XCircle className="h-3 w-3 mr-1" />
          Declined
        </Badge>;
      case 'completed':
        return <Badge variant="outline" className="bg-blue-50 text-blue-700 border-blue-200">
          <CheckCircle className="h-3 w-3 mr-1" />
          Completed
        </Badge>;
      default:
        return null;
    }
  };

  const getActionButton = (booking: BookingRequest) => {
    switch (booking.status) {
      case 'accepted':
        return (
          <Button 
            className="bg-gradient-primary"
            onClick={() => {
              const params = new URLSearchParams({
                type: 'coach',
                id: booking.id,
                date: booking.date,
                time: booking.time,
                price: booking.price.toString()
              });
              navigate(`/payment?${params.toString()}`);
            }}
          >
            <DollarSign className="h-4 w-4 mr-2" />
            Pay Now
          </Button>
        );
      case 'completed':
        return (
          <RatingFeedback
            coachId={booking.coachId}
            coachName={booking.coachName}
            coachAvatar={booking.coachAvatar}
            onSubmit={(rating, feedback) => {
              console.log('Rating submitted:', { rating, feedback, bookingId: booking.id });
            }}
          />
        );
      default:
        return null;
    }
  };

  return (
    <div className="min-h-screen bg-background">
      <div className="container mx-auto px-4 py-8">
        <div className="mb-8">
          <h1 className="text-3xl font-bold text-foreground mb-2">My Bookings</h1>
          <p className="text-muted-foreground">Track your coaching session requests and payments</p>
        </div>

        <div className="space-y-6">
          {bookings.map((booking) => (
            <Card key={booking.id} className="hover:shadow-medium transition-all duration-300">
              <CardHeader className="pb-4">
                <div className="flex items-start justify-between">
                  <div className="flex items-center gap-4">
                    <Avatar className="h-16 w-16">
                      <AvatarImage src={booking.coachAvatar} alt={booking.coachName} />
                      <AvatarFallback className="text-lg">{booking.coachName.charAt(0)}</AvatarFallback>
                    </Avatar>
                    <div>
                      <CardTitle className="text-xl text-foreground mb-1">
                        {booking.coachName}
                      </CardTitle>
                      <Badge variant="secondary" className="bg-primary/10 text-primary mb-2">
                        {booking.sport} Coach
                      </Badge>
                      <div className="flex items-center gap-4 text-sm text-muted-foreground">
                        <div className="flex items-center gap-1">
                          <Calendar className="h-4 w-4" />
                          <span>Requested on {format(new Date(booking.requestDate), 'MMM dd, yyyy')}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="text-right">
                    {getStatusBadge(booking.status)}
                    <div className="text-2xl font-bold text-primary mt-2">
                      ${booking.price}<span className="text-sm text-muted-foreground">/hour</span>
                    </div>
                  </div>
                </div>
              </CardHeader>
              
              <CardContent>
                <div className="grid md:grid-cols-2 gap-6">
                  <div className="space-y-4">
                    <div className="flex items-start gap-3">
                      <Calendar className="h-5 w-5 text-primary mt-0.5" />
                      <div>
                        <p className="font-medium text-foreground">Session Date & Time</p>
                        <p className="text-muted-foreground">
                          {format(new Date(booking.date), 'EEEE, MMMM dd, yyyy')} at {booking.time}
                        </p>
                      </div>
                    </div>
                    
                    <div className="flex items-start gap-3">
                      <MapPin className="h-5 w-5 text-primary mt-0.5" />
                      <div>
                        <p className="font-medium text-foreground">Venue</p>
                        <p className="text-muted-foreground">{booking.venue}</p>
                      </div>
                    </div>
                  </div>

                  <div className="space-y-4">
                    {booking.sessionNotes && (
                      <div className="bg-muted/50 rounded-lg p-4">
                        <p className="font-medium text-foreground mb-2">Session Notes</p>
                        <p className="text-sm text-muted-foreground">{booking.sessionNotes}</p>
                      </div>
                    )}
                    
                    <div className="flex justify-end">
                      {getActionButton(booking)}
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>

        {bookings.length === 0 && (
          <Card className="text-center py-12">
            <CardContent>
              <Clock className="h-16 w-16 mx-auto text-muted-foreground mb-4" />
              <h3 className="text-xl font-semibold text-foreground mb-2">No Bookings Yet</h3>
              <p className="text-muted-foreground mb-6">
                Start by browsing coaches and sending your first booking request!
              </p>
              <Button className="bg-gradient-primary">
                Find Coaches
              </Button>
            </CardContent>
          </Card>
        )}
      </div>
    </div>
  );
};

export default MyBookings;
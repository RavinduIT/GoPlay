import { useState } from 'react';
import { useSearchParams, useNavigate } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Textarea } from '@/components/ui/textarea';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { ArrowLeft, Star, Award, Calendar as CalendarIcon, MapPin, Clock, Globe, Trophy, Users, Send, MessageSquare } from 'lucide-react';
import { Separator } from '@/components/ui/separator';
import { format } from 'date-fns';
import { cn } from '@/lib/utils';
import { useToast } from '@/hooks/use-toast';
import coachTrainingImage from '@/assets/coach-training.jpg';
import coachCertification from '@/assets/coach-certification.jpg';

const CoachProfile = () => {
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();
  const { toast } = useToast();
  const coachId = searchParams.get('id') || '1';

  // Booking form state
  const [selectedDate, setSelectedDate] = useState<Date>();
  const [selectedTime, setSelectedTime] = useState('');
  const [venue, setVenue] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);

  // Mock coach data - in a real app this would come from an API
  const coachData = {
    1: {
      id: 1,
      name: 'John Martinez',
      sport: 'Football',
      experience: 8,
      rating: 4.9,
      reviews: 156,
      price: 45,
      location: 'Downtown',
      avatar: coachTrainingImage,
      coverImage: coachCertification,
      gallery: [coachTrainingImage, coachCertification, coachTrainingImage, coachCertification],
      specializations: ['Youth Training', 'Professional Coaching', 'Tactical Analysis'],
      certifications: ['UEFA B License', 'Sports Science Diploma', 'First Aid Certified'],
      availability: ['Mon', 'Wed', 'Fri', 'Sat'],
      availableTimes: ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00', '18:00'],
      languages: ['English', 'Spanish'],
      bio: 'Former professional player with 8 years coaching experience. Specialized in youth development and tactical training.',
      fullDescription: `John Martinez is a dedicated football coach with over 8 years of professional coaching experience. He has worked with players of all levels, from youth teams to semi-professional clubs. His coaching philosophy focuses on developing technical skills, tactical awareness, and mental strength.

John's journey in football began as a professional player where he played for several clubs in the regional league. After his playing career, he transitioned into coaching and has since helped hundreds of players improve their game.

His expertise includes:
- Individual skill development
- Team tactical strategies  
- Mental preparation and sports psychology
- Youth development programs
- Goalkeeper training
- Fitness and conditioning`,
      achievements: [
        'Led youth team to regional championship (2022)',
        'Developed 15+ players who went on to professional contracts',
        'UEFA B License holder',
        'Sports Science Diploma graduate',
        'Over 500+ successful training sessions'
      ],
      testimonials: [
        {
          name: 'Sarah Johnson',
          rating: 5,
          comment: 'John helped me improve my technical skills tremendously. His training methods are excellent and he really cares about his players development.',
          date: '2024-01-10'
        },
        {
          name: 'Mike Wilson',
          rating: 5,
          comment: 'Best coach I have worked with. Professional, knowledgeable and patient. Highly recommended for anyone serious about improving their game.',
          date: '2024-01-05'
        },
        {
          name: 'Emily Chen',
          rating: 4,
          comment: 'Great coaching sessions with clear improvement visible after just a few weeks. John knows how to motivate and push you to be better.',
          date: '2023-12-28'
        }
      ],
      pastWork: [
        {
          title: 'Youth Development Coach',
          organization: 'City Football Academy',
          period: '2020-2024',
          description: 'Lead coach for U-16 and U-18 teams, focusing on technical development and tactical awareness.'
        },
        {
          title: 'Assistant Coach',
          organization: 'Regional Semi-Pro Club',
          period: '2018-2020',
          description: 'Assisted with first team training, specialized in set pieces and defensive organization.'
        },
        {
          title: 'Private Coaching',
          organization: 'Independent',
          period: '2016-Present',
          description: 'Providing one-on-one coaching sessions for players of all ages and skill levels.'
        }
      ]
    }
  };

  const coach = coachData[coachId] || coachData[1];

  const handleBookingRequest = async () => {
    if (!selectedDate || !selectedTime || !venue.trim()) {
      toast({
        title: "Missing Information",
        description: "Please fill in all required fields: date, time, and venue.",
        variant: "destructive"
      });
      return;
    }

    setIsSubmitting(true);
    
    // Simulate API call
    setTimeout(() => {
      toast({
        title: "Request Sent!",
        description: `Your coaching request has been sent to ${coach.name}. You'll receive a notification when they respond.`,
      });
      
      // Reset form
      setSelectedDate(undefined);
      setSelectedTime('');
      setVenue('');
      setIsSubmitting(false);
    }, 1500);
  };

  return (
    <div className="min-h-screen bg-background">
      {/* Header with Cover Image */}
      <div className="relative h-64 bg-gradient-hero overflow-hidden">
        <div 
          className="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-30"
          style={{ backgroundImage: `url(${coach.coverImage})` }}
        />
        <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
        <div className="container mx-auto px-4 relative h-full flex items-end pb-8">
          <div className="flex items-end gap-6">
            <Avatar className="h-32 w-32 border-4 border-white/20">
              <AvatarImage src={coach.avatar} alt={coach.name} />
              <AvatarFallback className="text-4xl">{coach.name.charAt(0)}</AvatarFallback>
            </Avatar>
            <div className="text-white">
              <h1 className="text-4xl font-bold mb-2">{coach.name}</h1>
              <div className="flex items-center gap-4 mb-2">
                <Badge className="bg-primary/90 text-primary-foreground">
                  {coach.sport} Coach
                </Badge>
                <div className="flex items-center gap-1">
                  <Star className="h-5 w-5 fill-yellow-400 text-yellow-400" />
                  <span className="font-semibold">{coach.rating}</span>
                  <span className="text-white/80">({coach.reviews} reviews)</span>
                </div>
              </div>
              <div className="flex items-center gap-4 text-white/90">
                <div className="flex items-center gap-1">
                  <Award className="h-4 w-4" />
                  <span>{coach.experience} years experience</span>
                </div>
                <div className="flex items-center gap-1">
                  <MapPin className="h-4 w-4" />
                  <span>{coach.location}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div className="container mx-auto px-4 py-8">
        <Button 
          variant="outline" 
          onClick={() => navigate(-1)}
          className="mb-6"
        >
          <ArrowLeft className="h-4 w-4 mr-2" />
          Back
        </Button>

        <div className="grid lg:grid-cols-3 gap-8">
          {/* Main Content */}
          <div className="lg:col-span-2 space-y-8">
            {/* Coach Gallery */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Users className="h-5 w-5" />
                  Coach Gallery
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="grid grid-cols-2 gap-4">
                  {coach.gallery.map((image, index) => (
                    <div key={index} className="relative h-32 rounded-lg overflow-hidden group">
                      <img 
                        src={image} 
                        alt={`${coach.name} - Image ${index + 1}`}
                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                      />
                      <div className="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300" />
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>

            {/* About Section */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Users className="h-5 w-5" />
                  About {coach.name}
                </CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-muted-foreground mb-4">{coach.bio}</p>
                <div className="whitespace-pre-line text-foreground">
                  {coach.fullDescription}
                </div>
              </CardContent>
            </Card>

            {/* Specializations & Certifications */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Award className="h-5 w-5" />
                  Expertise & Certifications
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-6">
                <div>
                  <h4 className="font-semibold mb-3">Specializations</h4>
                  <div className="flex flex-wrap gap-2">
                    {coach.specializations.map((spec) => (
                      <Badge key={spec} variant="secondary" className="bg-primary/10 text-primary">
                        {spec}
                      </Badge>
                    ))}
                  </div>
                </div>
                <div>
                  <h4 className="font-semibold mb-3">Certifications</h4>
                  <div className="flex flex-wrap gap-2">
                    {coach.certifications.map((cert) => (
                      <Badge key={cert} variant="outline" className="bg-accent/10 text-accent">
                        {cert}
                      </Badge>
                    ))}
                  </div>
                </div>
                <div>
                  <h4 className="font-semibold mb-3">Languages</h4>
                  <div className="flex flex-wrap gap-2">
                    {coach.languages.map((lang) => (
                      <Badge key={lang} variant="outline">
                        <Globe className="h-3 w-3 mr-1" />
                        {lang}
                      </Badge>
                    ))}
                  </div>
                </div>
              </CardContent>
            </Card>

            {/* Achievements */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Trophy className="h-5 w-5" />
                  Achievements
                </CardTitle>
              </CardHeader>
              <CardContent>
                <ul className="space-y-3">
                  {coach.achievements.map((achievement, index) => (
                    <li key={index} className="flex items-start gap-3">
                      <div className="w-2 h-2 bg-primary rounded-full mt-2 flex-shrink-0" />
                      <span className="text-foreground">{achievement}</span>
                    </li>
                  ))}
                </ul>
              </CardContent>
            </Card>

            {/* Past Work Experience */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Calendar className="h-5 w-5" />
                  Work Experience
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-6">
                  {coach.pastWork.map((work, index) => (
                    <div key={index}>
                      <div className="flex items-start justify-between mb-2">
                        <div>
                          <h4 className="font-semibold text-foreground">{work.title}</h4>
                          <p className="text-primary font-medium">{work.organization}</p>
                        </div>
                        <Badge variant="outline" className="text-xs">
                          {work.period}
                        </Badge>
                      </div>
                      <p className="text-muted-foreground text-sm">{work.description}</p>
                      {index < coach.pastWork.length - 1 && <Separator className="mt-6" />}
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>

            {/* Reviews & Testimonials */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Star className="h-5 w-5" />
                  Reviews & Testimonials
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-6">
                  {coach.testimonials.map((testimonial, index) => (
                    <div key={index} className="border border-border/20 rounded-lg p-4">
                      <div className="flex items-center justify-between mb-3">
                        <div className="flex items-center gap-3">
                          <Avatar className="h-10 w-10">
                            <AvatarFallback>{testimonial.name.charAt(0)}</AvatarFallback>
                          </Avatar>
                          <div>
                            <p className="font-medium text-foreground">{testimonial.name}</p>
                            <div className="flex items-center gap-1">
                              {Array.from({ length: testimonial.rating }).map((_, i) => (
                                <Star key={i} className="h-3 w-3 fill-yellow-400 text-yellow-400" />
                              ))}
                            </div>
                          </div>
                        </div>
                        <span className="text-xs text-muted-foreground">
                          {new Date(testimonial.date).toLocaleDateString()}
                        </span>
                      </div>
                      <p className="text-muted-foreground italic">"{testimonial.comment}"</p>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
          </div>

          {/* Sidebar - Booking Request Form */}
          <div>
            <Card className="sticky top-8">
              <CardHeader>
                <CardTitle>Send Booking Request</CardTitle>
                <div className="text-3xl font-bold text-primary">
                  ${coach.price}<span className="text-sm text-muted-foreground">/hour</span>
                </div>
                <p className="text-sm text-muted-foreground">
                  Send a request to the coach. Payment is only required after they accept.
                </p>
              </CardHeader>
              <CardContent className="space-y-6">
                {/* Date Selection */}
                <div>
                  <label className="block text-sm font-medium text-card-foreground mb-2">
                    Select Date *
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
                    <PopoverContent className="w-auto p-0 z-50" align="start">
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

                {/* Time Selection */}
                <div>
                  <label className="block text-sm font-medium text-card-foreground mb-2">
                    Select Time *
                  </label>
                  <div className="grid grid-cols-3 gap-2">
                    {coach.availableTimes.map((time) => (
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
                </div>

                {/* Venue */}
                <div>
                  <label className="block text-sm font-medium text-card-foreground mb-2">
                    Venue Details *
                  </label>
                  <Textarea
                    placeholder="Please specify the venue address or location where you'd like the coaching session to take place..."
                    value={venue}
                    onChange={(e) => setVenue(e.target.value)}
                    className="min-h-[80px]"
                  />
                </div>

                <Separator />
                <div className="space-y-4">
                  <div className="flex items-center justify-between text-sm">
                    <span>Session duration:</span>
                    <span className="font-medium">60 minutes</span>
                  </div>
                  <div className="flex items-center justify-between text-sm">
                    <span>Response time:</span>
                    <span className="font-medium">Within 2 hours</span>
                  </div>
                  <div className="flex items-center justify-between text-sm">
                    <span>Cancellation:</span>
                    <span className="font-medium">24h notice</span>
                  </div>
                </div>

                <Button 
                  className="w-full bg-gradient-primary text-lg py-6"
                  onClick={handleBookingRequest}
                  disabled={isSubmitting || !selectedDate || !selectedTime || !venue.trim()}
                >
                  {isSubmitting ? (
                    <>Processing...</>
                  ) : (
                    <>
                      <Send className="mr-2 h-5 w-5" />
                      Send Request
                    </>
                  )}
                </Button>

                <p className="text-xs text-muted-foreground text-center">
                  The coach will review your request and respond within 2 hours
                </p>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  );
};

export default CoachProfile;
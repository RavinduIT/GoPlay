import { useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Plus, Edit, Trash2, User, Calendar, Clock, DollarSign, Star, Award } from 'lucide-react';
import coachBackground from '@/assets/coach-background.jpg';
import coachCertification from '@/assets/coach-certification.jpg';
import coachTrainingImage from '@/assets/coach-training.jpg';

const AdminCoach = () => {
  const [coaches, setCoaches] = useState([
    {
      id: 1,
      name: 'Michael Johnson',
      sport: 'Basketball',
      experience: 8,
      rating: 4.9,
      price: 75,
      image: coachTrainingImage,
      status: 'active',
      certifications: ['FIBA Level 2', 'Youth Development'],
      specializations: ['Shooting', 'Defense']
    },
    {
      id: 2,
      name: 'Sarah Williams',
      sport: 'Tennis',
      experience: 12,
      rating: 4.8,
      price: 85,
      image: coachTrainingImage,
      status: 'active',
      certifications: ['ITF Level 3', 'Sports Psychology'],
      specializations: ['Technique', 'Mental Game']
    }
  ]);

  const [newCoach, setNewCoach] = useState({
    name: '',
    sport: '',
    experience: '',
    price: '',
    bio: ''
  });

  const handleAddCoach = () => {
    if (newCoach.name && newCoach.sport) {
      const coach = {
        id: Date.now(),
        ...newCoach,
        experience: parseInt(newCoach.experience),
        price: parseFloat(newCoach.price),
        rating: 4.0,
        image: coachTrainingImage,
        status: 'active',
        certifications: [],
        specializations: []
      };
      setCoaches([...coaches, coach]);
      setNewCoach({ name: '', sport: '', experience: '', price: '', bio: '' });
    }
  };

  const stats = [
    { title: 'Total Coaches', value: coaches.length, icon: User, color: 'text-blue-600' },
    { title: 'Today Earnings', value: '$640', icon: DollarSign, color: 'text-green-600' },
    { title: 'Sessions Today', value: '8', icon: Calendar, color: 'text-purple-600' },
    { title: 'Avg Rating', value: '4.8', icon: Star, color: 'text-yellow-600' }
  ];

  return (
    <div className="min-h-screen bg-background">
      {/* Header */}
      <div 
        className="relative h-64 bg-cover bg-center flex items-center justify-center"
        style={{ backgroundImage: `url(${coachBackground})` }}
      >
        <div className="absolute inset-0 bg-black/60"></div>
        <div className="relative text-center text-white">
          <h1 className="text-4xl font-bold mb-2">Coach Admin Dashboard</h1>
          <p className="text-xl opacity-90">Manage your coaching services and sessions</p>
        </div>
      </div>

      <div className="container mx-auto px-4 py-8">
        {/* Stats */}
        <div className="grid md:grid-cols-4 gap-6 mb-8">
          {stats.map((stat, index) => (
            <Card key={index}>
              <CardContent className="p-6">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm text-muted-foreground">{stat.title}</p>
                    <p className="text-2xl font-bold">{stat.value}</p>
                  </div>
                  <stat.icon className={`h-8 w-8 ${stat.color}`} />
                </div>
              </CardContent>
            </Card>
          ))}
        </div>

        <Tabs defaultValue="coaches" className="space-y-6">
          <TabsList>
            <TabsTrigger value="coaches">Coaches</TabsTrigger>
            <TabsTrigger value="sessions">Sessions</TabsTrigger>
            <TabsTrigger value="certifications">Certifications</TabsTrigger>
            <TabsTrigger value="analytics">Analytics</TabsTrigger>
          </TabsList>

          <TabsContent value="coaches" className="space-y-6">
            {/* Add Coach */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Plus className="h-5 w-5" />
                  Add New Coach
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="grid md:grid-cols-2 gap-4 mb-4">
                  <div>
                    <Label htmlFor="coachName">Coach Name</Label>
                    <Input
                      id="coachName"
                      value={newCoach.name}
                      onChange={(e) => setNewCoach({...newCoach, name: e.target.value})}
                      placeholder="Enter coach name"
                    />
                  </div>
                  <div>
                    <Label htmlFor="sport">Sport</Label>
                    <Select value={newCoach.sport} onValueChange={(value) => setNewCoach({...newCoach, sport: value})}>
                      <SelectTrigger>
                        <SelectValue placeholder="Select sport" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="Basketball">Basketball</SelectItem>
                        <SelectItem value="Tennis">Tennis</SelectItem>
                        <SelectItem value="Football">Football</SelectItem>
                        <SelectItem value="Volleyball">Volleyball</SelectItem>
                        <SelectItem value="Swimming">Swimming</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                  <div>
                    <Label htmlFor="experience">Experience (years)</Label>
                    <Input
                      id="experience"
                      type="number"
                      value={newCoach.experience}
                      onChange={(e) => setNewCoach({...newCoach, experience: e.target.value})}
                      placeholder="0"
                    />
                  </div>
                  <div>
                    <Label htmlFor="coachPrice">Price per Hour ($)</Label>
                    <Input
                      id="coachPrice"
                      type="number"
                      value={newCoach.price}
                      onChange={(e) => setNewCoach({...newCoach, price: e.target.value})}
                      placeholder="0.00"
                    />
                  </div>
                  <div className="md:col-span-2">
                    <Label htmlFor="bio">Biography</Label>
                    <Textarea
                      id="bio"
                      value={newCoach.bio}
                      onChange={(e) => setNewCoach({...newCoach, bio: e.target.value})}
                      placeholder="Enter coach biography and specializations"
                    />
                  </div>
                  <div className="md:col-span-2">
                    <Button onClick={handleAddCoach} className="w-full">
                      Add Coach
                    </Button>
                  </div>
                </div>
              </CardContent>
            </Card>

            {/* Coaches List */}
            <Card>
              <CardHeader>
                <CardTitle>Your Coaching Team</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {coaches.map((coach) => (
                    <div key={coach.id} className="flex items-center gap-4 p-4 border rounded-lg">
                      <img 
                        src={coach.image} 
                        alt={coach.name}
                        className="w-20 h-20 object-cover rounded-full"
                      />
                      <div className="flex-1">
                        <h3 className="font-semibold">{coach.name}</h3>
                        <p className="text-sm text-muted-foreground">{coach.sport} • {coach.experience} years</p>
                        <div className="flex items-center gap-2 mt-1">
                          <Badge variant="outline">${coach.price}/hour</Badge>
                          <Badge variant="secondary" className="flex items-center gap-1">
                            <Star className="h-3 w-3 fill-yellow-400 text-yellow-400" />
                            {coach.rating}
                          </Badge>
                          <Badge variant={coach.status === 'active' ? 'default' : 'secondary'}>
                            {coach.status}
                          </Badge>
                        </div>
                        <div className="flex flex-wrap gap-1 mt-2">
                          {coach.specializations.map((spec) => (
                            <Badge key={spec} variant="outline" className="text-xs">
                              {spec}
                            </Badge>
                          ))}
                        </div>
                      </div>
                      <div className="flex gap-2">
                        <Button variant="outline" size="sm">
                          <Edit className="h-4 w-4" />
                        </Button>
                        <Button variant="outline" size="sm" className="text-red-600 hover:text-red-700">
                          <Trash2 className="h-4 w-4" />
                        </Button>
                      </div>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="sessions">
            <Card>
              <CardHeader>
                <CardTitle>Coaching Sessions</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  <div className="flex items-center justify-between p-4 border rounded-lg">
                    <div>
                      <p className="font-semibold">Basketball Training</p>
                      <p className="text-sm text-muted-foreground">Alex Johnson • 1 hour • 15:00-16:00</p>
                    </div>
                    <div className="text-right">
                      <p className="font-semibold">$75.00</p>
                      <Badge>Scheduled</Badge>
                    </div>
                  </div>
                  <div className="flex items-center justify-between p-4 border rounded-lg">
                    <div>
                      <p className="font-semibold">Tennis Lesson</p>
                      <p className="text-sm text-muted-foreground">Emma Davis • 1.5 hours • 10:00-11:30</p>
                    </div>
                    <div className="text-right">
                      <p className="font-semibold">$127.50</p>
                      <Badge variant="secondary">Completed</Badge>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="certifications">
            <Card>
              <CardHeader>
                <CardTitle>Certifications & Qualifications</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  <div className="flex items-center gap-4 p-4 border rounded-lg">
                    <img 
                      src={coachCertification} 
                      alt="Certification"
                      className="w-16 h-16 object-cover rounded"
                    />
                    <div className="flex-1">
                      <h3 className="font-semibold">FIBA Level 2 Certification</h3>
                      <p className="text-sm text-muted-foreground">Basketball Coaching • Valid until 2025</p>
                    </div>
                    <Badge variant="default">
                      <Award className="h-3 w-3 mr-1" />
                      Verified
                    </Badge>
                  </div>
                  <div className="flex items-center gap-4 p-4 border rounded-lg">
                    <img 
                      src={coachCertification} 
                      alt="Certification"
                      className="w-16 h-16 object-cover rounded"
                    />
                    <div className="flex-1">
                      <h3 className="font-semibold">Sports Psychology Certificate</h3>
                      <p className="text-sm text-muted-foreground">Mental Training • Valid until 2026</p>
                    </div>
                    <Badge variant="default">
                      <Award className="h-3 w-3 mr-1" />
                      Verified
                    </Badge>
                  </div>
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="analytics">
            <Card>
              <CardHeader>
                <CardTitle>Performance Analytics</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="grid md:grid-cols-2 gap-6">
                  <div>
                    <h3 className="font-semibold mb-4">Most Popular Sports</h3>
                    <div className="space-y-2">
                      <div className="flex justify-between">
                        <span>Basketball</span>
                        <span>55%</span>
                      </div>
                      <div className="flex justify-between">
                        <span>Tennis</span>
                        <span>35%</span>
                      </div>
                      <div className="flex justify-between">
                        <span>Football</span>
                        <span>10%</span>
                      </div>
                    </div>
                  </div>
                  <div>
                    <h3 className="font-semibold mb-4">Monthly Earnings</h3>
                    <p className="text-2xl font-bold text-green-600">$5,670</p>
                    <p className="text-sm text-muted-foreground">+22% from last month</p>
                  </div>
                </div>
              </CardContent>
            </Card>
          </TabsContent>
        </Tabs>
      </div>
    </div>
  );
};

export default AdminCoach;
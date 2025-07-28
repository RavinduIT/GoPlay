import { useSearchParams, useNavigate } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, Clock, User, Share2, Heart } from 'lucide-react';
import heroSportsImage from '@/assets/hero-sports.jpg';
import sportsComplexImage from '@/assets/sports-complex.jpg';
import tennisCourtImage from '@/assets/tennis-court.jpg';
import basketballCourtImage from '@/assets/basketball-court.jpg';

const NewsDetails = () => {
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();
  const newsId = searchParams.get('id');

  // Mock news data - in a real app this would come from an API
  const newsData = {
    1: {
      id: 1,
      title: 'World Cup 2024 Preparations Begin',
      image: heroSportsImage,
      summary: 'Teams gear up for the biggest tournament of the year',
      content: `
        The 2024 World Cup is fast approaching, and teams from around the globe are intensifying their preparation efforts. With just months to go before the tournament kicks off, national squads are fine-tuning their strategies and conditioning their players for what promises to be the most competitive World Cup in recent memory.

        Coaching staff are working around the clock to ensure their teams are in peak physical and mental condition. Training camps have been established in various locations, with teams focusing on both technical skills and tactical awareness.

        The tournament is expected to showcase some of the world's finest football talent, with several young players making their World Cup debuts alongside seasoned veterans. The combination of experience and fresh energy is creating an exciting dynamic within many squads.

        Fans around the world are eagerly anticipating the start of the tournament, with ticket sales breaking previous records. The host nation has been preparing infrastructure and facilities to accommodate the massive influx of visitors expected during the tournament period.
      `,
      author: 'John Smith',
      date: '2024-01-15',
      category: 'Football',
      readTime: '5 min read'
    },
    2: {
      id: 2,
      title: 'Tennis Championship Finals',
      image: tennisCourtImage,
      summary: 'Epic showdown at Wimbledon this weekend',
      content: `
        The tennis world is buzzing with excitement as we approach the championship finals this weekend. Two exceptional athletes have fought their way through intense competition to reach this pinnacle moment of the season.

        The road to the finals has been filled with remarkable matches, stunning upsets, and displays of athletic prowess that have kept fans on the edge of their seats. Both finalists have demonstrated not only technical excellence but also the mental fortitude required to compete at the highest level.

        Weather conditions are expected to be perfect for the final matches, with clear skies and mild temperatures providing ideal playing conditions. The court has been meticulously prepared to ensure the best possible playing surface for this prestigious event.

        Experts predict this could be one of the most memorable finals in recent tennis history, with both players bringing unique strengths and playing styles to the court. The contrast in their approaches should create fascinating tactical battles throughout the match.
      `,
      author: 'Sarah Johnson',
      date: '2024-01-14',
      category: 'Tennis',
      readTime: '4 min read'
    },
    3: {
      id: 3,
      title: 'Basketball League Season Starts',
      image: basketballCourtImage,
      summary: 'New season brings exciting matchups and talent',
      content: `
        The new basketball season has officially begun, bringing with it a fresh wave of excitement and anticipation. This year's league promises to be one of the most competitive yet, with several teams making significant roster changes during the off-season.

        Young talent is making its mark early in the season, with rookie players showing exceptional skill and adaptability. The integration of new players with established team cores is creating fascinating dynamics on the court.

        Coaching strategies have evolved significantly, with teams adopting new offensive and defensive schemes. The emphasis on three-point shooting and fast-paced gameplay continues to transform how the sport is played at the professional level.

        Fan engagement has reached new heights, with attendance figures showing strong growth compared to previous seasons. The combination of exciting gameplay and community outreach programs has helped basketball maintain its position as one of the most popular sports globally.
      `,
      author: 'Mike Wilson',
      date: '2024-01-13',
      category: 'Basketball',
      readTime: '4 min read'
    },
    4: {
      id: 4,
      title: 'Olympic Training Facilities Open',
      image: sportsComplexImage,
      summary: 'State-of-the-art facilities ready for athletes',
      content: `
        A new era in athletic training has begun with the opening of cutting-edge Olympic training facilities. These state-of-the-art centers represent the pinnacle of sports science and technology, designed to help athletes reach their maximum potential.

        The facilities feature advanced equipment for strength training, cardiovascular conditioning, and sport-specific skill development. Recovery areas include hydrotherapy pools, cryotherapy chambers, and massage therapy suites staffed by certified professionals.

        Sports scientists and coaches are working together to develop personalized training programs for each athlete. The integration of data analytics and performance monitoring technology allows for precise tracking of progress and optimization of training routines.

        The opening of these facilities marks a significant investment in the future of sports, with the goal of developing athletes who can compete at the highest levels of international competition. The impact on athletic performance is expected to be substantial in the coming years.
      `,
      author: 'Lisa Brown',
      date: '2024-01-12',
      category: 'Olympics',
      readTime: '6 min read'
    }
  };

  const currentNews = newsData[newsId] || newsData[1];

  return (
    <div className="min-h-screen bg-background">
      <div className="container mx-auto px-4 py-8 max-w-4xl">
        <Button 
          variant="outline" 
          onClick={() => navigate(-1)}
          className="mb-6"
        >
          <ArrowLeft className="h-4 w-4 mr-2" />
          Back
        </Button>

        <Card className="overflow-hidden">
          {/* Hero Image */}
          <div className="relative h-96 overflow-hidden">
            <img 
              src={currentNews.image} 
              alt={currentNews.title}
              className="w-full h-full object-cover"
            />
            <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent" />
            <div className="absolute bottom-6 left-6 right-6 text-white">
              <Badge className="mb-3 bg-primary/90">
                {currentNews.category}
              </Badge>
              <h1 className="text-3xl md:text-4xl font-bold mb-2">
                {currentNews.title}
              </h1>
              <p className="text-lg text-white/90">
                {currentNews.summary}
              </p>
            </div>
          </div>

          <CardHeader>
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-4 text-sm text-muted-foreground">
                <div className="flex items-center gap-1">
                  <User className="h-4 w-4" />
                  <span>{currentNews.author}</span>
                </div>
                <div className="flex items-center gap-1">
                  <Clock className="h-4 w-4" />
                  <span>{new Date(currentNews.date).toLocaleDateString()}</span>
                </div>
                <Badge variant="outline">{currentNews.readTime}</Badge>
              </div>
              <div className="flex gap-2">
                <Button variant="outline" size="sm">
                  <Heart className="h-4 w-4" />
                </Button>
                <Button variant="outline" size="sm">
                  <Share2 className="h-4 w-4" />
                </Button>
              </div>
            </div>
          </CardHeader>

          <CardContent className="prose prose-lg max-w-none">
            <div className="text-foreground leading-relaxed space-y-4">
              {currentNews.content.split('\n\n').map((paragraph, index) => (
                <p key={index} className="text-muted-foreground">
                  {paragraph.trim()}
                </p>
              ))}
            </div>
          </CardContent>
        </Card>

        {/* Related News */}
        <div className="mt-12">
          <h2 className="text-2xl font-bold text-foreground mb-6">Related News</h2>
          <div className="grid md:grid-cols-2 gap-6">
            {Object.values(newsData)
              .filter(news => news.id !== currentNews.id)
              .slice(0, 2)
              .map((relatedNews) => (
                <Card 
                  key={relatedNews.id}
                  className="cursor-pointer hover:shadow-medium transition-all duration-300"
                  onClick={() => navigate(`/news-details?id=${relatedNews.id}`)}
                >
                  <div className="relative h-48 overflow-hidden">
                    <img 
                      src={relatedNews.image} 
                      alt={relatedNews.title}
                      className="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
                    <div className="absolute bottom-4 left-4 right-4 text-white">
                      <h3 className="font-bold line-clamp-2">{relatedNews.title}</h3>
                      <p className="text-sm text-white/80 mt-1">{relatedNews.summary}</p>
                    </div>
                  </div>
                </Card>
              ))}
          </div>
        </div>
      </div>
    </div>
  );
};

export default NewsDetails;
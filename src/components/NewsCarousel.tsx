import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Card } from '@/components/ui/card';
import heroSportsImage from '@/assets/hero-sports.jpg';
import sportsComplexImage from '@/assets/sports-complex.jpg';
import tennisCourtImage from '@/assets/tennis-court.jpg';
import basketballCourtImage from '@/assets/basketball-court.jpg';

const NewsCarousel = () => {
  const navigate = useNavigate();
  const [currentIndex, setCurrentIndex] = useState(0);

  const newsItems = [
    {
      id: 1,
      title: 'World Cup 2024 Preparations Begin',
      image: heroSportsImage,
      summary: 'Teams gear up for the biggest tournament of the year',
      date: '2024-01-15'
    },
    {
      id: 2,
      title: 'Tennis Championship Finals',
      image: tennisCourtImage,
      summary: 'Epic showdown at Wimbledon this weekend',
      date: '2024-01-14'
    },
    {
      id: 3,
      title: 'Basketball League Season Starts',
      image: basketballCourtImage,
      summary: 'New season brings exciting matchups and talent',
      date: '2024-01-13'
    },
    {
      id: 4,
      title: 'Olympic Training Facilities Open',
      image: sportsComplexImage,
      summary: 'State-of-the-art facilities ready for athletes',
      date: '2024-01-12'
    },
    {
      id: 5,
      title: 'Youth Sports Programs Launch',
      image: heroSportsImage,
      summary: 'Inspiring the next generation of athletes',
      date: '2024-01-11'
    },
    {
      id: 6,
      title: 'Sports Technology Innovation',
      image: sportsComplexImage,
      summary: 'AI revolutionizes training methods worldwide',
      date: '2024-01-10'
    }
  ];

  // Auto-scroll functionality - continuous movement
  useEffect(() => {
    const interval = setInterval(() => {
      setCurrentIndex((prevIndex) => (prevIndex + 1) % newsItems.length);
    }, 5000); // Change every 5 seconds for slower, more comfortable viewing

    return () => clearInterval(interval);
  }, [newsItems.length]);

  const handleNewsClick = (newsId: number) => {
    navigate(`/news-details?id=${newsId}`);
  };

  return (
    <div className="relative overflow-hidden rounded-xl bg-gradient-to-r from-primary/5 to-accent/5 p-4">
      {/* Auto-scrolling news container */}
      <div 
        className="flex transition-transform duration-1000 ease-linear gap-4"
        style={{ 
          transform: `translateX(-${currentIndex * (100 / 3)}%)`, // Show 3 items at once
          width: `${(newsItems.length * 100) / 3}%`
        }}
      >
        {[...newsItems, ...newsItems].map((news, index) => ( // Duplicate for seamless loop
          <div 
            key={`${news.id}-${index}`}
            className="flex-shrink-0"
            style={{ width: `${100 / (newsItems.length * 2)}%`, minWidth: '280px' }}
          >
            <Card 
              className="cursor-pointer group hover:shadow-strong transition-all duration-300 hover:-translate-y-2 overflow-hidden border-0 bg-white/90 backdrop-blur-sm"
              onClick={() => handleNewsClick(news.id)}
            >
              <div className="relative h-48 overflow-hidden">
                <img 
                  src={news.image} 
                  alt={news.title}
                  className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent" />
                
                {/* News content overlay */}
                <div className="absolute bottom-0 left-0 right-0 p-4 text-white">
                  <h3 className="text-base font-bold mb-1 line-clamp-2 group-hover:text-primary-glow transition-colors">
                    {news.title}
                  </h3>
                  <p className="text-sm text-white/90 line-clamp-2 mb-2">
                    {news.summary}
                  </p>
                  <div className="flex justify-between items-center">
                    <p className="text-xs text-white/70">
                      {new Date(news.date).toLocaleDateString()}
                    </p>
                    <div className="w-2 h-2 bg-primary rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                  </div>
                </div>

                {/* Hover effect overlay */}
                <div className="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              </div>
            </Card>
          </div>
        ))}
      </div>

      {/* Progress indicators */}
      <div className="flex justify-center mt-6 space-x-2">
        {newsItems.map((_, index) => (
          <button
            key={index}
            className={`w-2 h-2 rounded-full transition-all duration-300 ${
              index === currentIndex % newsItems.length 
                ? 'bg-primary scale-125 shadow-medium' 
                : 'bg-muted-foreground/30 hover:bg-muted-foreground/50'
            }`}
            onClick={() => setCurrentIndex(index)}
          />
        ))}
      </div>

    </div>
  );
};

export default NewsCarousel;
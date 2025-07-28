import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Textarea } from '@/components/ui/textarea';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Star, MessageSquare, ThumbsUp } from 'lucide-react';
import { useToast } from '@/hooks/use-toast';

interface RatingFeedbackProps {
  coachId: string;
  coachName: string;
  coachAvatar: string;
  onSubmit?: (rating: number, feedback: string) => void;
}

const RatingFeedback = ({ coachId, coachName, coachAvatar, onSubmit }: RatingFeedbackProps) => {
  const [isOpen, setIsOpen] = useState(false);
  const [rating, setRating] = useState(0);
  const [hoveredRating, setHoveredRating] = useState(0);
  const [feedback, setFeedback] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const { toast } = useToast();

  const handleSubmit = async () => {
    if (rating === 0) {
      toast({
        title: "Rating Required",
        description: "Please select a star rating before submitting.",
        variant: "destructive"
      });
      return;
    }

    if (feedback.trim().length < 10) {
      toast({
        title: "Feedback Too Short",
        description: "Please provide at least 10 characters of feedback.",
        variant: "destructive"
      });
      return;
    }

    setIsSubmitting(true);

    // Simulate API call
    setTimeout(() => {
      toast({
        title: "Thank You!",
        description: "Your rating and feedback has been submitted successfully.",
      });
      
      onSubmit?.(rating, feedback);
      
      // Reset form
      setRating(0);
      setFeedback('');
      setIsOpen(false);
      setIsSubmitting(false);
    }, 1000);
  };

  const handleStarClick = (starRating: number) => {
    setRating(starRating);
  };

  const handleStarHover = (starRating: number) => {
    setHoveredRating(starRating);
  };

  const displayRating = hoveredRating || rating;

  const getRatingText = (rating: number) => {
    switch (rating) {
      case 1: return "Poor";
      case 2: return "Fair";
      case 3: return "Good";
      case 4: return "Very Good";
      case 5: return "Excellent";
      default: return "Rate your experience";
    }
  };

  return (
    <Dialog open={isOpen} onOpenChange={setIsOpen}>
      <DialogTrigger asChild>
        <Button variant="outline" className="flex items-center gap-2">
          <Star className="h-4 w-4" />
          Rate Coach
        </Button>
      </DialogTrigger>
      <DialogContent className="sm:max-w-md">
        <DialogHeader>
          <DialogTitle className="flex items-center gap-3">
            <Avatar className="h-12 w-12">
              <AvatarImage src={coachAvatar} alt={coachName} />
              <AvatarFallback>{coachName.charAt(0)}</AvatarFallback>
            </Avatar>
            <div>
              <p className="text-lg font-semibold">Rate {coachName}</p>
              <p className="text-sm text-muted-foreground">Share your experience</p>
            </div>
          </DialogTitle>
        </DialogHeader>
        
        <Card className="border-0 shadow-none">
          <CardContent className="p-0 space-y-6">
            {/* Star Rating */}
            <div className="text-center space-y-4">
              <div className="flex justify-center items-center gap-2">
                {[1, 2, 3, 4, 5].map((star) => (
                  <button
                    key={star}
                    onClick={() => handleStarClick(star)}
                    onMouseEnter={() => handleStarHover(star)}
                    onMouseLeave={() => setHoveredRating(0)}
                    className="transition-all duration-200 hover:scale-110"
                  >
                    <Star
                      className={`h-8 w-8 cursor-pointer transition-colors ${
                        star <= displayRating
                          ? 'fill-yellow-400 text-yellow-400'
                          : 'text-gray-300 hover:text-yellow-300'
                      }`}
                    />
                  </button>
                ))}
              </div>
              <p className="text-lg font-medium text-foreground">
                {getRatingText(displayRating)}
              </p>
            </div>

            {/* Feedback Textarea */}
            <div className="space-y-2">
              <label className="text-sm font-medium text-foreground">
                Tell others about your experience
              </label>
              <Textarea
                placeholder="How was your coaching session? What did you like most? Any suggestions for improvement?"
                value={feedback}
                onChange={(e) => setFeedback(e.target.value)}
                className="min-h-[100px] resize-none"
                maxLength={500}
              />
              <div className="text-xs text-muted-foreground text-right">
                {feedback.length}/500 characters
              </div>
            </div>

            {/* Tips */}
            <div className="bg-muted/50 rounded-lg p-4">
              <div className="flex items-start gap-3">
                <MessageSquare className="h-5 w-5 text-primary mt-0.5 flex-shrink-0" />
                <div className="space-y-2">
                  <p className="text-sm font-medium text-foreground">Helpful review tips:</p>
                  <ul className="text-xs text-muted-foreground space-y-1">
                    <li>• Mention the coach's teaching style and communication</li>
                    <li>• Share specific improvements you noticed</li>
                    <li>• Comment on punctuality and professionalism</li>
                    <li>• Be honest and constructive</li>
                  </ul>
                </div>
              </div>
            </div>

            {/* Submit Button */}
            <Button
              onClick={handleSubmit}
              disabled={isSubmitting || rating === 0 || feedback.trim().length < 10}
              className="w-full bg-gradient-primary"
            >
              {isSubmitting ? (
                <>Submitting...</>
              ) : (
                <>
                  <ThumbsUp className="mr-2 h-4 w-4" />
                  Submit Review
                </>
              )}
            </Button>
          </CardContent>
        </Card>
      </DialogContent>
    </Dialog>
  );
};

export default RatingFeedback;
import { useNavigate } from 'react-router-dom';
import { CheckCircle, Package, ArrowLeft } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const PaymentSuccess = () => {
  const navigate = useNavigate();

  return (
    <div className="min-h-screen bg-background flex items-center justify-center">
      <div className="container mx-auto px-4 py-8 max-w-md">
        <Card className="text-center">
          <CardHeader>
            <div className="mx-auto mb-4 w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
              <CheckCircle className="h-8 w-8 text-green-600" />
            </div>
            <CardTitle className="text-2xl font-bold text-green-600">Payment Successful!</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              <p className="text-muted-foreground">
                Thank you for your purchase! Your order has been successfully placed.
              </p>
              
              <div className="bg-muted/50 p-4 rounded-lg">
                <div className="flex items-center gap-2 mb-2">
                  <Package className="h-4 w-4" />
                  <span className="font-medium">Order Status</span>
                </div>
                <p className="text-sm text-muted-foreground">
                  Your order is being processed and you will receive a confirmation email shortly.
                  The shop owner will contact you within 24 hours to arrange delivery.
                </p>
              </div>

              <div className="space-y-3 pt-4">
                <Button 
                  onClick={() => navigate('/shop')}
                  className="w-full bg-gradient-primary"
                >
                  Continue Shopping
                </Button>
                <Button 
                  onClick={() => navigate('/')}
                  variant="outline"
                  className="w-full"
                >
                  <ArrowLeft className="mr-2 h-4 w-4" />
                  Back to Home
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
};

export default PaymentSuccess;
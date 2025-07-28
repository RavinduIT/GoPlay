import { useEffect, useRef, useState } from 'react';
import { Loader } from '@googlemaps/js-api-loader';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { MapPin } from 'lucide-react';

interface GoogleMapProps {
  address?: string;
  lat?: number;
  lng?: number;
  height?: string;
  className?: string;
}

const GoogleMap = ({ 
  address = "Downtown Area", 
  lat = 40.7128, 
  lng = -74.0060, 
  height = "400px",
  className = ""
}: GoogleMapProps) => {
  const mapRef = useRef<HTMLDivElement>(null);
  const [map, setMap] = useState<google.maps.Map | null>(null);
  const [apiKey, setApiKey] = useState('');
  const [isLoaded, setIsLoaded] = useState(false);
  const [error, setError] = useState('');

  const initializeMap = async (key: string) => {
    if (!mapRef.current || !key) return;

    try {
      const loader = new Loader({
        apiKey: key,
        version: 'weekly',
        libraries: ['places']
      });

      await loader.load();

      const mapInstance = new google.maps.Map(mapRef.current, {
        center: { lat, lng },
        zoom: 15,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: false,
        styles: [
          {
            featureType: 'poi',
            elementType: 'labels',
            stylers: [{ visibility: 'off' }]
          }
        ]
      });

      // Add marker
      new google.maps.Marker({
        position: { lat, lng },
        map: mapInstance,
        title: address,
        icon: {
          path: google.maps.SymbolPath.CIRCLE,
          scale: 8,
          fillColor: '#3b82f6',
          fillOpacity: 1,
          strokeColor: '#ffffff',
          strokeWeight: 2,
        }
      });

      // Add info window
      const infoWindow = new google.maps.InfoWindow({
        content: `
          <div style="padding: 8px;">
            <h3 style="margin: 0 0 8px 0; font-weight: bold;">${address}</h3>
            <p style="margin: 0; color: #666;">Sports Ground Location</p>
          </div>
        `
      });

      const marker = new google.maps.Marker({
        position: { lat, lng },
        map: mapInstance,
        title: address
      });

      marker.addListener('click', () => {
        infoWindow.open(mapInstance, marker);
      });

      setMap(mapInstance);
      setIsLoaded(true);
      setError('');
    } catch (err) {
      setError('Failed to load map. Please check your API key.');
      console.error('Map loading error:', err);
    }
  };

  const handleApiKeySubmit = () => {
    if (apiKey.trim()) {
      initializeMap(apiKey.trim());
    }
  };

  if (!isLoaded && !error) {
    return (
      <Card className={`p-6 ${className}`} style={{ height }}>
        <div className="text-center space-y-4">
          <MapPin className="h-12 w-12 mx-auto text-muted-foreground" />
          <div>
            <h3 className="font-semibold mb-2">Enter Google Maps API Key</h3>
            <p className="text-sm text-muted-foreground mb-4">
              To view the location map, please enter your Google Maps API key.
              You can get one from <a href="https://console.cloud.google.com/" target="_blank" rel="noopener noreferrer" className="text-primary underline">Google Cloud Console</a>.
            </p>
            <div className="flex gap-2">
              <Input
                type="text"
                placeholder="Enter Google Maps API Key"
                value={apiKey}
                onChange={(e) => setApiKey(e.target.value)}
                className="flex-1"
              />
              <Button onClick={handleApiKeySubmit} disabled={!apiKey.trim()}>
                Load Map
              </Button>
            </div>
          </div>
        </div>
      </Card>
    );
  }

  if (error) {
    return (
      <Card className={`p-6 ${className}`} style={{ height }}>
        <div className="text-center space-y-4">
          <MapPin className="h-12 w-12 mx-auto text-destructive" />
          <div>
            <h3 className="font-semibold text-destructive mb-2">Map Error</h3>
            <p className="text-sm text-muted-foreground mb-4">{error}</p>
            <Button onClick={() => window.location.reload()} variant="outline">
              Try Again
            </Button>
          </div>
        </div>
      </Card>
    );
  }

  return (
    <Card className={`overflow-hidden ${className}`} style={{ height }}>
      <div ref={mapRef} className="w-full h-full" />
    </Card>
  );
};

export default GoogleMap;
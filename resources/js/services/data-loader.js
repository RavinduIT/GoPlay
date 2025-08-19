// Data Loader for GoPlay Application
class DataLoader {
    constructor() {
        this.cache = new Map();
        this.baseUrl = '../data/';
    }

    // Generic fetch method with error handling and caching
    async fetchData(filename, cacheKey = null) {
        const key = cacheKey || filename;
        
        // Return cached data if available
        if (this.cache.has(key)) {
            return this.cache.get(key);
        }

        try {
            const response = await fetch(`${this.baseUrl}${filename}`);
            
            if (!response.ok) {
                throw new Error(`Failed to load ${filename}: ${response.status} ${response.statusText}`);
            }
            
            const data = await response.json();
            
            // Cache the data
            this.cache.set(key, data);
            
            return data;
        } catch (error) {
            console.error('Error loading data:', error);
            throw new Error(`Unable to load ${filename}. Please check your internet connection and try again.`);
        }
    }

    // Load facilities data
    async loadFacilities() {
        try {
            const data = await this.fetchData('grounds.json', 'facilities');
            return {
                facilities: data.facilities || [],
                locations: data.locations || [],
                sports: data.sports || []
            };
        } catch (error) {
            console.error('Error loading facilities:', error);
            // Return empty arrays as fallback
            return {
                facilities: [],
                locations: [],
                sports: []
            };
        }
    }

    // Load coaches data
    async loadCoaches() {
        try {
            const data = await this.fetchData('coaches.json', 'coaches');
            return data.coaches || [];
        } catch (error) {
            console.error('Error loading coaches:', error);
            return [];
        }
    }

    // Load news data
    async loadNews() {
        try {
            const data = await this.fetchData('news.json', 'news');
            return data.news || [];
        } catch (error) {
            console.error('Error loading news:', error);
            return [];
        }
    }

    // Load products data
    async loadProducts() {
        try {
            const data = await this.fetchData('products.json', 'products');
            return data.products || [];
        } catch (error) {
            console.error('Error loading products:', error);
            return [];
        }
    }

    // Load user data (with authentication check)
    async loadUserData() {
        try {
            const data = await this.fetchData('users.json', 'users');
            return data.users || [];
        } catch (error) {
            console.error('Error loading user data:', error);
            return [];
        }
    }

    // Filter facilities by various criteria
    filterFacilities(facilities, filters = {}) {
        return facilities.filter(facility => {
            // Search term filter
            if (filters.searchTerm) {
                const searchLower = filters.searchTerm.toLowerCase();
                const matchesName = facility.name.toLowerCase().includes(searchLower);
                const matchesAddress = facility.address.toLowerCase().includes(searchLower);
                const matchesSports = facility.sports.some(sport => 
                    sport.toLowerCase().includes(searchLower)
                );
                
                if (!matchesName && !matchesAddress && !matchesSports) {
                    return false;
                }
            }

            // Location filter
            if (filters.location) {
                const locationMatch = facility.address.toLowerCase().includes(
                    filters.location.replace('-', ' ')
                );
                if (!locationMatch) return false;
            }

            // Sport type filter
            if (filters.sportType) {
                const sportMatch = facility.sports.some(sport => 
                    sport.toLowerCase().includes(filters.sportType.toLowerCase())
                );
                if (!sportMatch) return false;
            }

            // Price range filter
            if (filters.priceRange) {
                const price = facility.pricePerHour;
                if (filters.priceRange === '0-30' && price > 30) return false;
                if (filters.priceRange === '30-50' && (price <= 30 || price > 50)) return false;
                if (filters.priceRange === '50+' && price <= 50) return false;
            }

            // Rating filter
            if (filters.minRating) {
                if (facility.rating < filters.minRating) return false;
            }

            return true;
        });
    }

    // Sort facilities by various criteria
    sortFacilities(facilities, sortBy = 'rating') {
        const sortedFacilities = [...facilities];
        
        switch (sortBy) {
            case 'rating':
                return sortedFacilities.sort((a, b) => b.rating - a.rating);
            
            case 'price-low':
                return sortedFacilities.sort((a, b) => a.pricePerHour - b.pricePerHour);
            
            case 'price-high':
                return sortedFacilities.sort((a, b) => b.pricePerHour - a.pricePerHour);
            
            case 'name':
                return sortedFacilities.sort((a, b) => a.name.localeCompare(b.name));
            
            case 'distance':
                // This would require user location - placeholder for now
                return sortedFacilities;
            
            default:
                return sortedFacilities;
        }
    }

    // Get facility by ID
    getFacilityById(facilities, id) {
        return facilities.find(facility => facility.id === parseInt(id));
    }

    // Get facilities by sport
    getFacilitiesBySport(facilities, sport) {
        return facilities.filter(facility => 
            facility.sports.some(s => s.toLowerCase() === sport.toLowerCase())
        );
    }

    // Get facilities within price range
    getFacilitiesByPriceRange(facilities, minPrice, maxPrice) {
        return facilities.filter(facility => 
            facility.pricePerHour >= minPrice && facility.pricePerHour <= maxPrice
        );
    }

    // Get top-rated facilities
    getTopRatedFacilities(facilities, count = 5) {
        return this.sortFacilities(facilities, 'rating').slice(0, count);
    }

    // Clear cache
    clearCache() {
        this.cache.clear();
    }

    // Get cache info (for debugging)
    getCacheInfo() {
        return {
            size: this.cache.size,
            keys: Array.from(this.cache.keys())
        };
    }
}

// Create global instance
window.dataLoader = new DataLoader();

// Export for module systems if available
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DataLoader;
}
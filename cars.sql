-- Insert a car
INSERT INTO cars (
  name, category, seats, transmission, price, rating, featured, image, description,
  features, specifications, location_1, location_2
) VALUES 
('Audi R8', 'Supercar', 2, 'Automatic', 25000, 4.8, 1, 'a1.png',
 'The Audi R8 combines luxury and performance, featuring a powerful V10 engine and a sleek design.',
 '• Quattro AWD\n• LED Headlights\n• Virtual Cockpit\n• Bang & Olufsen Sound System\n• Carbon Fiber Accents',
 '• 5.2L V10\n• 610 hp\n• 7-speed dual-clutch\n• 0-100 km/h in 3.2s\n• Top speed: 330 km/h',
 'Chikkamagaluru', 'Uttar Kannada'),

('BMW M2', 'Sports', 2, 'Automatic', 3500.0, 4.8, 1, 'b1.png',
 'The BMW M2 is a high-performance sports car designed for enthusiasts who seek thrilling driving experiences. It combines sleek aesthetics with power-packed performance.',
 '• M Sport Exhaust\n• Adaptive M Suspension\n• BMW iDrive 6.0\n• Apple CarPlay\n• Dual-zone automatic climate control',
 '• Engine: 3.0L Twin-Turbo Inline-6\n• Power: 405 HP\n• Torque: 406 lb-ft\n• 0-60 mph: 4.1 seconds\n• Top Speed: 174 mph',
 'Bidar', 'Ballari'),

('BMW M4', 'Coupe', 4, 'Automatic', 19500, 4.7, 1, 'b2.png',
 'The BMW M4 offers a perfect blend of performance and luxury, making it a top choice for enthusiasts.',
 '• M Sport Differential\n• Adaptive M Suspension\n• Harman Kardon Audio\n• Carbon Fiber Roof\n• M Drive Modes',
 '• 3.0L Twin-Turbo I6\n• 503 hp\n• 8-speed automatic\n• 0-100 km/h in 3.5s\n• Top speed: 250 km/h',
 'Dharwad', 'Mysuru'),

('BMW X6', 'SUV', 5, 'Automatic', 18000, 4.6, 0, 'b3.png',
 'The BMW X6 combines the versatility of an SUV with the elegance of a coupe, offering a unique driving experience.',
 '• xDrive AWD\n• Panoramic Sunroof\n• Gesture Control\n• Adaptive LED Headlights\n• Leather Upholstery',
 '• 3.0L Turbocharged I6\n• 375 hp\n• 8-speed automatic\n• 0-100 km/h in 5.5s\n• Top speed: 250 km/h',
 'Gadag', 'Belagavi'),

('Chevrolet Camaro', 'Muscle Car', 4, 'Manual', 15000, 4.5, 0, 'c1.png',
 'The Chevrolet Camaro is an iconic muscle car known for its aggressive styling and powerful performance.',
 '• Performance Suspension\n• Brembo Brakes\n• Dual-Mode Exhaust\n• Apple CarPlay\n• Rear Vision Camera',
 '• 6.2L V8\n• 455 hp\n• 6-speed manual\n• 0-100 km/h in 4.0s\n• Top speed: 290 km/h',
 'Bengaluru Urban', 'Bagalkot'),

('Chevrolet Corvette C7', 'Sports Car', 2, 'Manual', 22000, 4.7, 1, 'c2.png',
 'The Corvette C7 offers a thrilling driving experience with its advanced engineering and design.',
 '• Magnetic Ride Control\n• Performance Data Recorder\n• Carbon Fiber Hood\n• Bose Audio\n• Heads-Up Display',
 '• 6.2L V8\n• 460 hp\n• 7-speed manual\n• 0-100 km/h in 3.8s\n• Top speed: 300 km/h',
 'Davanagere', 'Bengaluru Rural'),

('Toyota LC 300', 'SUV', 7, 'Automatic', 18000, 4.6, 0, 't2.png',
 'The Toyota Land Cruiser 300 is renowned for its off-road prowess and luxurious comfort.',
 '• Multi-Terrain Select\n• Crawl Control\n• Leather Seats\n• JBL Audio System\n• Advanced Safety Features',
 '• 3.5L Twin-Turbo V6\n• 409 hp\n• 10-speed automatic\n• 0-100 km/h in 6.7s\n• Top speed: 210 km/h',
 'Haveri', 'Chikkamagaluru'),

('Toyota FJ Cruiser', 'SUV', 5, 'Automatic', 12000, 4.4, 0, 't1.png',
 'The Toyota FJ Cruiser stands out with its retro design and exceptional off-road capabilities.',
 '• Part-Time 4WD\n• Skid Plates\n• Water-Resistant Seats\n• Compass & Inclinometer\n• Rear Parking Sensors',
 '• 4.0L V6\n• 270 hp\n• 5-speed automatic\n• 0-100 km/h in 7.8s\n• Top speed: 175 km/h',
 'Ballari', 'Bidar'),

('Ferrari F12tdf', 'Supercar', 2, 'Automatic', 55000, 4.9, 1, 'f1.png',
 'The Ferrari F12tdf is a limited-edition masterpiece, delivering unmatched performance and exclusivity.',
 '• Rear-Wheel Steering\n• Carbon Fiber Body Panels\n• Racing Seats\n• High-Performance Brakes\n• Aerodynamic Enhancements',
 '• 6.3L V12\n• 769 hp\n• 7-speed dual-clutch\n• 0-100 km/h in 2.9s\n• Top speed: 340 km/h',
 'Mysuru', 'Bagalkot'),

('Jaguar XF', 'Sedan', 5, 'Automatic', 14000, 4.5, 0, 'j1.png',
 'The Jaguar XF combines British elegance with dynamic performance, offering a refined driving experience.',
 '• Meridian Sound System\n• Adaptive Dynamics\n• LED Headlights\n• Leather Upholstery\n• Touch Pro Infotainment',
 '• 2.0L Turbocharged I4\n• 247 hp\n• 8-speed automatic\n• 0-100 km/h in 6.5s\n• Top speed: 250 km/h',
 'Bengaluru Rural', 'Dharwad');
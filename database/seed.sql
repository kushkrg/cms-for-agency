-- ============================================
-- Evolvcode CMS Seed Data
-- Content extracted from evolvcode.com
-- ============================================

-- Default Admin User (password: admin123 - CHANGE IN PRODUCTION!)
INSERT INTO admins (username, email, password_hash) VALUES
('admin', 'sales@evolvcode.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Site Settings
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'Evolvcode Solutions'),
('site_tagline', 'Digital Marketing Agency | Best Web Development in Patna'),
('site_description', 'Boost your business with our digital solutions. Join Evolvcode now!'),
('contact_email', 'sales@evolvcode.com'),
('contact_phone', '+91-9229045881'),
('whatsapp_number', '919229045881'),
('address', 'Patna, Bihar, India'),
('facebook_url', ''),
('twitter_url', ''),
('linkedin_url', ''),
('instagram_url', ''),
('logo_path', '/assets/images/logo.png'),
('footer_text', '© 2026 Evolvcode Solutions. All rights reserved.');

-- Portfolio Categories
INSERT INTO portfolio_categories (name, slug) VALUES
('Web Development', 'web-development'),
('E-commerce', 'ecommerce'),
('Digital Marketing', 'digital-marketing'),
('App Development', 'app-development');

-- Portfolio Projects
INSERT INTO projects (title, slug, client_name, short_description, content, category_id, is_featured, status, meta_title, meta_description) VALUES
(
    'Career Hike | Complete Website Development for Education Admission Consultancy',
    'career-hike-complete-website-development-for-education-admission-consultancy',
    'Career Hike',
    'Evolvcode Solutions is excited to have partnered with Career Hike, delivering a comprehensive website for education admission consultancy.',
    '<h2>Project Highlights</h2>
<h3>University and College Listing Features</h3>
<ul>
<li>Created a comprehensive database for universities and colleges with detailed profiles, making it easier for students to explore their options.</li>
<li>Implemented filters and search functionality to enable quick and accurate results based on location, ranking, and specialization.</li>
</ul>

<h3>Course Listing Features</h3>
<ul>
<li>Designed an organized structure for courses offered by various institutions with detailed descriptions, eligibility criteria, and application processes.</li>
<li>Enabled course comparison to help students make informed decisions.</li>
</ul>

<h3>Custom Design for Education Consultancy</h3>
<p>Developed a modern, intuitive design reflecting Career Hike''s mission of guiding students and parents through the complex admission process.</p>

<h3>Responsive and Mobile-Friendly Interface</h3>
<p>Ensured a seamless browsing experience across all devices for both students and parents.</p>

<h3>Service Pages</h3>
<p>Highlighted key services, including career counseling, admission guidance, scholarship assistance, and visa consultation.</p>

<h3>Student-Friendly Features</h3>
<ul>
<li>Integrated inquiry forms and appointment scheduling for one-on-one guidance.</li>
<li>Developed a "Success Stories" section to inspire and motivate aspiring students.</li>
</ul>

<h3>Blog and Resource Section</h3>
<p>Added a blog to share admission tips, study abroad guides, and the latest updates in the education industry.</p>

<h3>SEO Optimization</h3>
<p>Enhanced the website''s online presence to ensure it reaches students and parents looking for reliable admission consultancy services.</p>

<h3>Analytics Integration</h3>
<p>Implemented tracking tools to monitor website engagement and user interactions for continuous improvement.</p>',
    1,
    TRUE,
    'published',
    'Career Hike Website Development | Evolvcode Solutions',
    'Check out the new Career Hike website that simplifies student admissions!'
),
(
    'Benison IVF | Complete Business Website Development',
    'benison-ivf-complete-business-website-development',
    'Benison IVF',
    'Evolvcode Solutions is proud to have successfully delivered a comprehensive business website for Benison IVF.',
    '<h2>Project Overview</h2>
<p>Evolvcode Solutions is proud to have successfully delivered a comprehensive business website for Benison IVF, a leading fertility clinic.</p>

<h3>Key Features</h3>
<ul>
<li>Clean, professional design reflecting trust and medical expertise</li>
<li>Service pages highlighting IVF treatments and fertility solutions</li>
<li>Patient testimonials and success stories</li>
<li>Online appointment booking system</li>
<li>Doctor profiles and qualifications</li>
<li>Blog section for fertility tips and information</li>
<li>Mobile-responsive design for accessibility</li>
<li>SEO optimization for local search visibility</li>
</ul>',
    1,
    TRUE,
    'published',
    'Benison IVF | Complete Business Website Development',
    'Comprehensive business website development for Benison IVF fertility clinic.'
),
(
    'Vvalyou | WordPress to Shopify Migration with Advanced Features',
    'vvalyou-wordpress-to-shopify-migration-with-advanced-features',
    'Vvalyou',
    'Evolvcode Solutions proudly partnered with Vvalyou to migrate their e-commerce platform from WordPress to Shopify.',
    '<h2>Project Overview</h2>
<p>Evolvcode Solutions proudly partnered with Vvalyou to migrate their e-commerce platform from WordPress to Shopify with advanced features.</p>

<h3>Migration Highlights</h3>
<ul>
<li>Complete product catalog migration with all variants and images</li>
<li>Customer data and order history preservation</li>
<li>SEO URL redirects to maintain search rankings</li>
<li>Custom Shopify theme development matching brand identity</li>
<li>Advanced filtering and search functionality</li>
<li>Payment gateway integration</li>
<li>Inventory management system setup</li>
<li>Performance optimization for faster load times</li>
</ul>',
    2,
    TRUE,
    'published',
    'Vvalyou | WordPress to Shopify Migration with Advanced Features',
    'Complete WordPress to Shopify migration with advanced e-commerce features.'
);

-- Services
INSERT INTO services (title, slug, short_description, content, icon, features, sort_order, status, meta_title, meta_description) VALUES
(
    'Digital Marketing Services',
    'digital-marketing-services',
    'Unlock your business potential with our expert digital marketing strategies designed for measurable growth.',
    '<h2>Digital Marketing Services Designed for Measurable Growth</h2>
<p>We keep things simple, clear, and results-focused—taking your brand from "just online" to "growing online."</p>

<h3>Our Credentials and Expertise</h3>
<ul>
<li><strong>Certified Professionals:</strong> Our team holds Google Ads, Meta Ads, and HubSpot certifications.</li>
<li><strong>Proven Track Record:</strong> We''ve helped 50+ businesses increase traffic, leads, and revenue.</li>
<li><strong>Industry Experience:</strong> Over 5 years working across eCommerce, healthcare, tech, education, and local businesses.</li>
<li><strong>Data-First Approach:</strong> Every recommendation is based on analytics and market research.</li>
</ul>

<h3>Why Businesses Trust Our Digital Marketing Services</h3>
<ul>
<li><strong>Full Visibility:</strong> We provide detailed monthly reports and dashboards so you always know where your money is going.</li>
<li><strong>Ethical Practices:</strong> No black-hat tactics. We follow Google''s guidelines to protect your site from penalties.</li>
<li><strong>Client-Centric Approach:</strong> We listen to your business goals before building a custom strategy.</li>
<li><strong>Long-Term Focus:</strong> We build campaigns that grow with your business, not just short-term hacks.</li>
</ul>',
    'fa-chart-line',
    'SEO Services,PPC Advertising,Social Media Marketing,Content Marketing,Email Marketing,Analytics & Reporting',
    1,
    'published',
    'Customized Digital Marketing Services for Business Growth',
    'Unlock your business potential with our expert strategies!'
),
(
    'Search Engine Optimization',
    'search-engine-optimization-services',
    'Improve your online visibility and drive organic traffic with our comprehensive SEO services.',
    '<h2>Search Engine Optimization (SEO)</h2>
<p>Get found on Google and other search engines with our proven SEO strategies.</p>

<h3>Our SEO Services Include:</h3>
<ul>
<li><strong>Website Audit & Keyword Research:</strong> In-depth analysis of your site and competitors.</li>
<li><strong>On-Page SEO:</strong> Optimizing content, meta tags, headings, and internal links.</li>
<li><strong>Technical SEO:</strong> Fixing crawl issues, improving speed, and making your site mobile-friendly.</li>
<li><strong>Link Building:</strong> Acquiring high-quality backlinks that boost domain authority.</li>
<li><strong>Local SEO:</strong> Perfect for businesses targeting customers in a specific region.</li>
</ul>',
    'fa-search',
    'Keyword Research,On-Page SEO,Technical SEO,Link Building,Local SEO,SEO Audits',
    2,
    'published',
    'Search Engine Optimization Services | Evolvcode',
    'Improve your online visibility and drive organic traffic with our comprehensive SEO services.'
),
(
    'Social Media Marketing',
    'social-media-marketing',
    'Build your brand presence and engage with your audience across all major social media platforms.',
    '<h2>Social Media Marketing (SMM)</h2>
<p>Connect with your audience where they spend their time.</p>

<h3>Our Social Media Services Include:</h3>
<ul>
<li><strong>Content Calendar & Strategy:</strong> Professionally planned posts that align with your brand voice.</li>
<li><strong>Community Building:</strong> Engaging followers and turning them into loyal advocates.</li>
<li><strong>Paid Social Ads:</strong> Expanding reach and driving measurable ROI.</li>
<li><strong>Platform Management:</strong> Facebook, Instagram, LinkedIn, Twitter, and more.</li>
</ul>',
    'fa-share-alt',
    'Content Strategy,Community Management,Paid Social Ads,Influencer Marketing,Analytics & Reporting',
    3,
    'published',
    'Social Media Marketing Services | Evolvcode',
    'Build your brand presence and engage with your audience across all major social media platforms.'
),
(
    'PPC Services',
    'ppc-services',
    'Get immediate visibility and drive targeted traffic with our pay-per-click advertising services.',
    '<h2>Pay-Per-Click (PPC) Advertising</h2>
<p>Get immediate results with targeted paid advertising campaigns.</p>

<h3>Our PPC Services Include:</h3>
<ul>
<li><strong>Google Ads:</strong> Search, Display, Shopping, and Remarketing campaigns.</li>
<li><strong>Meta Ads:</strong> Targeted campaigns on Facebook and Instagram to drive sales.</li>
<li><strong>Performance Monitoring:</strong> Continuous A/B testing to improve click-through rates and lower cost per lead.</li>
</ul>
<p>Our approach is fully transparent — you own your ad accounts, and we share full data access.</p>',
    'fa-bullseye',
    'Google Ads,Facebook Ads,Instagram Ads,Remarketing,A/B Testing,ROI Optimization',
    4,
    'published',
    'PPC Services | Evolvcode',
    'Get immediate visibility and drive targeted traffic with our pay-per-click advertising services.'
),
(
    'Shopify Store Development',
    'shopify-store-development',
    'Launch your online store with our expert Shopify development services.',
    '<h2>Shopify Store Development</h2>
<p>Transform your online store with our expert Shopify solutions.</p>

<h3>Our Shopify Services Include:</h3>
<ul>
<li>Custom Shopify theme development</li>
<li>Store setup and configuration</li>
<li>Product catalog management</li>
<li>Payment gateway integration</li>
<li>Shopify app integration</li>
<li>Performance optimization</li>
<li>Migration from other platforms</li>
</ul>',
    'fa-shopping-bag',
    'Custom Theme Development,Store Setup,Payment Integration,App Integration,Migration Services',
    5,
    'published',
    'Top Shopify Store Development Solutions for Success',
    'Transform your online store with our expert Shopify solutions. Get your free consultation now!'
),
(
    'WordPress Development',
    'wordpress-development-services',
    'Get a powerful, customizable website with our professional WordPress development services.',
    '<h2>WordPress Development Services</h2>
<p>Transform your business with our expert WordPress services!</p>

<h3>Our WordPress Services Include:</h3>
<ul>
<li>Custom WordPress theme development</li>
<li>Plugin development and customization</li>
<li>WooCommerce setup and optimization</li>
<li>Website speed optimization</li>
<li>Security hardening</li>
<li>Maintenance and support</li>
</ul>

<h3>Why Choose Us:</h3>
<ul>
<li><strong>5+ Years Experience</strong></li>
<li><strong>90% Client Retention Rate</strong></li>
<li><strong>100+ Projects Delivered</strong></li>
</ul>',
    'fa-wordpress',
    'Theme Development,Plugin Development,WooCommerce,Speed Optimization,Security,Maintenance',
    6,
    'published',
    'Top WordPress Development Services for Your Business',
    'Transform your business with our expert WordPress services!'
),
(
    'Website Migration & Upgrade',
    'website-migration-upgrade-services',
    'Seamlessly migrate your website to a new platform or upgrade your existing site.',
    '<h2>Website Migration & Upgrade Services</h2>
<p>From old to new, we make it easy.</p>

<h3>Our Migration Services Include:</h3>
<ul>
<li>Platform-to-platform migration</li>
<li>Data and content migration</li>
<li>SEO preservation and redirects</li>
<li>Design refresh and modernization</li>
<li>Performance improvements</li>
<li>Post-migration support</li>
</ul>',
    'fa-exchange-alt',
    'Platform Migration,Data Migration,SEO Preservation,Design Refresh,Performance Upgrade',
    7,
    'published',
    'Website Migration & Upgrade Services | Evolvcode',
    'Seamlessly migrate your website to a new platform or upgrade your existing site.'
),
(
    'Ecommerce Web Development',
    'ecommerce-web-development-services',
    'Build a powerful online store that converts visitors into customers.',
    '<h2>Ecommerce Web Development Services</h2>
<p>Build an online store that drives sales.</p>

<h3>Our Ecommerce Services Include:</h3>
<ul>
<li>Custom ecommerce website development</li>
<li>Shopping cart and checkout optimization</li>
<li>Payment gateway integration</li>
<li>Inventory management</li>
<li>Order tracking systems</li>
<li>Multi-vendor marketplace development</li>
</ul>',
    'fa-store',
    'Custom Development,Cart Optimization,Payment Integration,Inventory Management,Order Tracking',
    8,
    'published',
    'Ecommerce Web Development Services | Evolvcode',
    'Build a powerful online store that converts visitors into customers.'
),
(
    'App Development Services',
    'app-development-services',
    'Transform your ideas into powerful mobile applications for iOS and Android.',
    '<h2>App Development Services</h2>
<p>Transform your ideas into powerful mobile applications.</p>

<h3>Our App Development Services Include:</h3>
<ul>
<li>iOS and Android app development</li>
<li>Cross-platform development (React Native, Flutter)</li>
<li>UI/UX design for mobile</li>
<li>API integration</li>
<li>App store optimization</li>
<li>Maintenance and updates</li>
</ul>',
    'fa-mobile-alt',
    'iOS Development,Android Development,Cross-Platform,UI/UX Design,API Integration,Maintenance',
    9,
    'published',
    'App Development Services | Evolvcode',
    'Transform your ideas into powerful mobile applications for iOS and Android.'
);

-- Blog Categories
INSERT INTO blog_categories (name, slug) VALUES
('Digital Marketing', 'digital-marketing'),
('Web Design', 'web-design'),
('Website Development', 'website-development'),
('Consulting', 'consulting'),
('Webflow Website', 'webflow-website');

-- Blog Posts
INSERT INTO posts (title, slug, excerpt, content, category_id, author, status, published_at, meta_title, meta_description) VALUES
(
    'How to Create a Winning Digital Marketing Strategy in 2025 (Complete Playbook)',
    'digital-marketing-strategy-playbook',
    'A digital marketing strategy is a documented plan that connects your business goals to your channels, messages, budgets, timelines, and KPIs.',
    '<h2>What Is a Digital Marketing Strategy (and Why It Fails Without a System)</h2>
<p>A digital marketing strategy is a documented plan that connects your business goals to your channels, messages, budgets, timelines, and KPIs.</p>

<p>Most strategies fail because they''re:</p>
<ul>
<li>Goal-less (no revenue linkage)</li>
<li>Channel-first (tactics without positioning)</li>
<li>Under-measured (weak analytics)</li>
<li>Or over-complicated (no weekly execution)</li>
</ul>

<p>This playbook will help you avoid these pitfalls and create a strategy that actually works.</p>',
    1,
    'Kush Kumar',
    'published',
    '2025-10-22 00:00:00',
    'How to Create a Winning Digital Marketing Strategy in 2025 (Complete Playbook)',
    'Learn how to create a winning digital marketing strategy with our complete playbook.'
),
(
    'Top Digital Marketing Agency in Patna: Evolvcode Solutions – Your #1 Choice',
    'best-digital-marketing-agency-in-patna',
    'Grow your business with simple, affordable, and effective digital marketing. From SEO and Social Media to PPC and Web Design.',
    '<h2>Best Digital Marketing Agency in Patna – Why Evolvcode Solutions is #1 Choice</h2>
<p>Patna''s #1 Digital Growth Partner</p>

<p>Grow your business with simple, affordable, and effective digital marketing. From SEO and Social Media to PPC and Web Design—we help Patna businesses get found and grow online.</p>

<h3>Our Services</h3>
<ul>
<li>Search Engine Optimization (SEO)</li>
<li>Social Media Marketing</li>
<li>Pay-Per-Click Advertising</li>
<li>Web Design & Development</li>
<li>Content Marketing</li>
</ul>',
    1,
    'Kush Kumar',
    'published',
    '2025-08-29 00:00:00',
    'Top Digital Marketing Agency in Patna: Evolvcode Solutions – Your #1 Choice',
    'Best Digital Marketing Agency in Patna – Why Evolvcode Solutions is #1 Choice'
),
(
    'The Ultimate Webflow Website Development Handbook',
    'the-ultimate-webflow-website-development-handbook',
    'In the ever-evolving world of web design, finding the right tools and platforms to bring your vision to life is crucial.',
    '<h2>The Ultimate Webflow Website Development Handbook</h2>
<p>In the ever-evolving world of web design, finding the right tools and platforms to bring your vision to life is crucial. Webflow has emerged as one of the most powerful and versatile website development platforms available today.</p>

<p>Whether you''re a seasoned developer or a novice looking to dip your toes into web design, Webflow offers a unique combination of visual design tools and robust functionality.</p>',
    5,
    'Anand Bajrangi',
    'published',
    '2024-06-02 00:00:00',
    'The Ultimate Webflow Website Development Handbook',
    'Complete guide to Webflow website development for designers and developers.'
),
(
    'Key Aspects of a Successful Website: Design, Content, SEO, and More',
    'key-aspects-of-a-successful-website-design-content-seo-and-more',
    'Discover the essential elements that make a website successful, from design and content to SEO and user experience.',
    '<h2>Key Aspects of a Successful Website</h2>
<p>A successful website is more than just an online presence—it''s a powerful tool that can drive business growth, build brand credibility, and engage your target audience.</p>

<h3>Essential Elements</h3>
<ul>
<li>Professional Design</li>
<li>Quality Content</li>
<li>SEO Optimization</li>
<li>User Experience</li>
<li>Mobile Responsiveness</li>
<li>Fast Loading Speed</li>
</ul>',
    4,
    'Kush Kumar',
    'published',
    '2024-05-15 00:00:00',
    'Key Aspects of a Successful Website: Design, Content, SEO, and More',
    'Discover the essential elements that make a website successful.'
);

-- About Page Content
INSERT INTO pages (title, slug, content, meta_title, meta_description, status) VALUES
(
    'About Us',
    'about',
    '<section class="who-we-are">
<h2>Who We Are</h2>
<p>We''re a team of creative minds, tech experts, and marketing pros who love helping businesses shine online. Based in India and serving clients worldwide, we bring a human touch to every digital project.</p>
<p>Whether it''s a website, a mobile app, or a digital campaign — we treat every project like our own.</p>
</section>

<section class="what-we-do">
<h2>What We Do</h2>
<ul>
<li>✨ Website Development – WordPress, Shopify, Custom</li>
<li>📈 Digital Marketing – SEO, Google Ads, Social Media</li>
<li>🛒 E-commerce Solutions – Online store setup, payments, tracking</li>
<li>🔄 Migration & Upgrades – From old to new, we make it easy</li>
<li>📊 PPC & Lead Generation – Get real leads that grow your business</li>
<li>🎨 Design & Branding – Logos, creatives, and clean UI/UX</li>
<li>🧠 Strategy & Consulting – Tech advice you can trust</li>
</ul>
</section>

<section class="who-we-work-with">
<h2>Who We Work With</h2>
<ul>
<li>🏪 Local Shops</li>
<li>🧑‍💼 Startups</li>
<li>🏢 Enterprises</li>
<li>💻 Freelancers & Coaches</li>
<li>🏥 Clinics & Medical Brands</li>
<li>🧵 Fashion & Lifestyle Stores</li>
<li>🛠️ Service-Based Companies</li>
</ul>
<p>No matter your industry, we''ll build what works best for your audience.</p>
</section>

<section class="how-we-work">
<h2>How We Work</h2>
<ol>
<li>Understand your needs 🤝</li>
<li>Plan smart 📋</li>
<li>Design with purpose 🎨</li>
<li>Build with care 🛠️</li>
<li>Launch with confidence 🚀</li>
<li>Support you always ❤️</li>
</ol>
<p>We don''t overcomplicate things. We keep you involved and informed at every step.</p>
</section>

<section class="trust-factors">
<h3>Transparency</h3>
<p>Transparency is our promise, ensuring open communication and clear expectations every step of the way.</p>

<h3>Experienced Team</h3>
<p>Our experienced team brings a wealth of expertise and knowledge to deliver exceptional results for every project.</p>

<h3>Result Guarantee</h3>
<p>With our result guarantee, we ensure you receive the outcomes you expect, backed by our commitment to your success.</p>
</section>',
    'Empowering Businesses with Smart Digital Solutions',
    'Transform your business with our innovative digital solutions. Get a free consultation today and watch your growth soar!',
    'published'
),
(
    'Contact Us',
    'contact',
    '<h2>Send us a message</h2>
<p>Ready to start your project? Get in touch with us today!</p>

<div class="contact-info">
<p><strong>Email:</strong> sales@evolvcode.com</p>
<p><strong>Phone:</strong> +91-9229045881</p>
<p><strong>WhatsApp:</strong> <a href="https://api.whatsapp.com/send/?phone=919229045881">Chat with us</a></p>
</div>',
    'Reliable Digital Marketing Team - Contact Us Today',
    'Reach out for expert digital marketing solutions. Our team ensures your website is optimized for success. Send us a message now!',
    'published'
);

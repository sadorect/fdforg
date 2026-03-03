#!/usr/bin/env python3
"""
Friends of the Deaf Foundation Website Scraper
Scrapes content from https://friendsofthedeaffoundation.org/ 
for Laravel migration
"""

import requests
from bs4 import BeautifulSoup
import json
import os
import re
import time
from urllib.parse import urljoin, urlparse
from urllib.request import urlretrieve
import html2text
from datetime import datetime
import csv

class WebsiteScraper:
    def __init__(self, base_url="https://friendsofthedeaffoundation.org/"):
        self.base_url = base_url
        self.session = requests.Session()
        self.session.headers.update({
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        })
        
        # Create output directories
        self.base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
        self.pages_dir = os.path.join(self.base_dir, 'pages')
        self.posts_dir = os.path.join(self.base_dir, 'posts')
        self.events_dir = os.path.join(self.base_dir, 'events')
        self.media_dir = os.path.join(self.base_dir, 'media')
        
        # Ensure directories exist
        os.makedirs(self.pages_dir, exist_ok=True)
        os.makedirs(self.posts_dir, exist_ok=True)
        os.makedirs(self.events_dir, exist_ok=True)
        os.makedirs(self.media_dir, exist_ok=True)
        
        # Track scraped URLs to avoid duplicates
        self.scraped_urls = set()
        
        # Initialize HTML to text converter
        self.h = html2text.HTML2Text()
        self.h.ignore_links = False
        self.h.ignore_images = False
        self.h.body_width = 0  # Don't wrap lines
        
    def get_page_content(self, url):
        """Fetch and parse a web page"""
        try:
            response = self.session.get(url, timeout=30)
            response.raise_for_status()
            return BeautifulSoup(response.content, 'html.parser')
        except Exception as e:
            print(f"Error fetching {url}: {e}")
            return None
    
    def clean_text(self, text):
        """Clean and normalize text content"""
        if not text:
            return ""
        
        # Remove extra whitespace
        text = re.sub(r'\s+', ' ', text)
        # Remove HTML entities
        text = re.sub(r'&[a-zA-Z0-9#]+;', '', text)
        # Strip leading/trailing whitespace
        text = text.strip()
        
        return text
    
    def extract_page_metadata(self, soup):
        """Extract page metadata from HTML head"""
        metadata = {}
        
        # Basic meta tags
        title_tag = soup.find('title')
        if title_tag:
            metadata['title'] = self.clean_text(title_tag.get_text())
        
        # Meta description
        desc_tag = soup.find('meta', attrs={'name': 'description'})
        if desc_tag:
            metadata['description'] = desc_tag.get('content', '')
        
        # Open Graph tags
        og_tags = soup.find_all('meta', attrs={'property': re.compile(r'^og:')})
        for tag in og_tags:
            property_name = tag.get('property', '').replace('og:', '')
            metadata[f'og_{property_name}'] = tag.get('content', '')
        
        # Twitter tags
        twitter_tags = soup.find_all('meta', attrs={'name': re.compile(r'^twitter:')})
        for tag in twitter_tags:
            name = tag.get('name', '').replace('twitter:', '')
            metadata[f'twitter_{name}'] = tag.get('content', '')
        
        return metadata
    
    def download_media(self, url, filename=None):
        """Download media files and save locally"""
        try:
            if not filename:
                parsed_url = urlparse(url)
                filename = os.path.basename(parsed_url.path)
                if not filename:
                    filename = f"media_{int(time.time())}"
            
            # Ensure unique filename
            base_name, ext = os.path.splitext(filename)
            counter = 1
            while os.path.exists(os.path.join(self.media_dir, filename)):
                filename = f"{base_name}_{counter}{ext}"
                counter += 1
            
            filepath = os.path.join(self.media_dir, filename)
            urlretrieve(url, filepath)
            return filename
        except Exception as e:
            print(f"Error downloading media {url}: {e}")
            return None
    
    def scrape_navigation(self, soup):
        """Extract navigation menu structure"""
        nav_items = []
        
        # Look for common navigation selectors
        nav_selectors = [
            'nav ul.menu',
            'nav ul.nav-menu', 
            'nav ul#menu-main',
            '.menu-primary-container ul',
            '.et_mobile_menu',
            '#top-menu',
            '.nav'
        ]
        
        for selector in nav_selectors:
            nav = soup.select_one(selector)
            if nav:
                for link in nav.find_all('a', href=True):
                    href = link['href']
                    if href.startswith('#') or 'javascript:' in href:
                        continue
                    
                    full_url = urljoin(self.base_url, href)
                    nav_items.append({
                        'text': self.clean_text(link.get_text()),
                        'url': full_url
                    })
                break
        
        return nav_items
    
    def scrape_main_page(self):
        """Scrape the homepage and main content structure"""
        print("Scraping main page...")
        soup = self.get_page_content(self.base_url)
        
        if not soup:
            return None
        
        # Extract page data
        page_data = {
            'url': self.base_url,
            'title': '',
            'content': '',
            'metadata': {},
            'navigation': [],
            'sections': [],
            'media_files': [],
            'scraped_at': datetime.now().isoformat()
        }
        
        # Extract metadata
        page_data['metadata'] = self.extract_page_metadata(soup)
        page_data['title'] = page_data['metadata'].get('title', 'Homepage')
        
        # Extract navigation
        page_data['navigation'] = self.scrape_navigation(soup)
        
        # Extract main content
        content_selectors = [
            '.entry-content',
            '.post-content',
            '.main-content',
            '#main-content',
            'main',
            '.et_pb_section',
            '.content-area'
        ]
        
        main_content = None
        for selector in content_selectors:
            main_content = soup.select_one(selector)
            if main_content:
                break
        
        if main_content:
            page_data['content'] = self.h.handle(str(main_content))
            
            # Extract media files
            images = main_content.find_all('img')
            for img in images:
                src = img.get('src')
                if src and not src.startswith('data:'):
                    full_url = urljoin(self.base_url, src)
                    filename = self.download_media(full_url)
                    if filename:
                        page_data['media_files'].append({
                            'original_url': full_url,
                            'local_filename': filename,
                            'alt_text': img.get('alt', ''),
                            'type': 'image'
                        })
        
        # Extract sections (for Divi theme)
        sections = soup.find_all(['section', '.et_pb_section'])
        for section in sections[:10]:  # Limit to first 10 sections
            section_data = {
                'class': section.get('class', []),
                'content': self.h.handle(str(section)),
                'images': []
            }
            
            # Extract images in section
            images = section.find_all('img')
            for img in images:
                src = img.get('src')
                if src and not src.startswith('data:'):
                    full_url = urljoin(self.base_url, src)
                    filename = self.download_media(full_url)
                    if filename:
                        section_data['images'].append(filename)
            
            page_data['sections'].append(section_data)
        
        # Save homepage data
        homepage_file = os.path.join(self.pages_dir, 'homepage.json')
        with open(homepage_file, 'w', encoding='utf-8') as f:
            json.dump(page_data, f, indent=2, ensure_ascii=False)
        
        print(f"Homepage saved to {homepage_file}")
        return page_data
    
    def discover_pages(self):
        """Discover all pages from navigation and sitemap"""
        discovered_urls = set([self.base_url])
        
        # Get main page and extract navigation
        soup = self.get_page_content(self.base_url)
        if soup:
            nav_items = self.scrape_navigation(soup)
            for item in nav_items:
                discovered_urls.add(item['url'])
        
        # Try to get sitemap
        sitemap_urls = [
            urljoin(self.base_url, '/sitemap.xml'),
            urljoin(self.base_url, '/sitemap_index.xml'),
            urljoin(self.base_url, '/wp-sitemap.xml')
        ]
        
        for sitemap_url in sitemap_urls:
            try:
                response = self.session.get(sitemap_url, timeout=10)
                if response.status_code == 200:
                    sitemap_soup = BeautifulSoup(response.content, 'xml')
                    urls = sitemap_soup.find_all('url')
                    for url in urls:
                        loc = url.find('loc')
                        if loc:
                            discovered_urls.add(loc.text)
                    break
            except:
                continue
        
        return list(discovered_urls)
    
    def scrape_posts(self):
        """Scrape blog posts and news articles"""
        print("Scraping blog posts...")
        
        # Try common blog URL patterns
        blog_urls = [
            urljoin(self.base_url, '/blog/'),
            urljoin(self.base_url, '/news/'),
            urljoin(self.base_url, '/category/news/'),
            urljoin(self.base_url, '/category/blog/'),
        ]
        
        posts_data = []
        
        for blog_url in blog_urls:
            soup = self.get_page_content(blog_url)
            if not soup:
                continue
            
            # Find post links
            post_links = []
            link_selectors = [
                'a[href*="/post/"]',
                'a[href*="/news/"]', 
                '.entry-title a',
                '.post-title a',
                'h2 a',
                'h3 a'
            ]
            
            for selector in link_selectors:
                links = soup.select(selector)
                for link in links:
                    href = link.get('href')
                    if href:
                        full_url = urljoin(self.base_url, href)
                        if full_url not in self.scraped_urls:
                            post_links.append(full_url)
            
            # Scrape each post
            for post_url in post_links[:20]:  # Limit to first 20 posts
                post_data = self.scrape_single_post(post_url)
                if post_data:
                    posts_data.append(post_data)
                    self.scraped_urls.add(post_url)
                time.sleep(1)  # Be respectful to the server
        
        # Save all posts
        posts_file = os.path.join(self.posts_dir, 'all_posts.json')
        with open(posts_file, 'w', encoding='utf-8') as f:
            json.dump(posts_data, f, indent=2, ensure_ascii=False)
        
        print(f"Scraped {len(posts_data)} posts")
        return posts_data
    
    def scrape_single_post(self, post_url):
        """Scrape a single blog post"""
        try:
            soup = self.get_page_content(post_url)
            if not soup:
                return None
            
            post_data = {
                'url': post_url,
                'title': '',
                'content': '',
                'excerpt': '',
                'author': '',
                'date': '',
                'categories': [],
                'tags': [],
                'featured_image': '',
                'metadata': {},
                'media_files': [],
                'scraped_at': datetime.now().isoformat()
            }
            
            # Extract title
            title_selectors = [
                'h1.entry-title',
                'h1.post-title',
                'h1',
                '.post-title h1'
            ]
            
            for selector in title_selectors:
                title_elem = soup.select_one(selector)
                if title_elem:
                    post_data['title'] = self.clean_text(title_elem.get_text())
                    break
            
            # Extract content
            content_selectors = [
                '.entry-content',
                '.post-content',
                '.content-area',
                'article .content'
            ]
            
            for selector in content_selectors:
                content_elem = soup.select_one(selector)
                if content_elem:
                    post_data['content'] = self.h.handle(str(content_elem))
                    break
            
            # Extract metadata
            post_data['metadata'] = self.extract_page_metadata(soup)
            
            # Extract author
            author_selectors = [
                '.author-name',
                '.post-author',
                '.byline .author',
                '.entry-author'
            ]
            
            for selector in author_selectors:
                author_elem = soup.select_one(selector)
                if author_elem:
                    post_data['author'] = self.clean_text(author_elem.get_text())
                    break
            
            # Extract date
            date_selectors = [
                '.entry-date',
                '.post-date',
                '.published',
                'time'
            ]
            
            for selector in date_selectors:
                date_elem = soup.select_one(selector)
                if date_elem:
                    post_data['date'] = date_elem.get('datetime', '') or self.clean_text(date_elem.get_text())
                    break
            
            # Extract categories and tags
            category_links = soup.select('.cat-links a, .category a')
            for link in category_links:
                category = self.clean_text(link.get_text())
                if category:
                    post_data['categories'].append(category)
            
            tag_links = soup.select('.tags-links a, .tag a')
            for link in tag_links:
                tag = self.clean_text(link.get_text())
                if tag:
                    post_data['tags'].append(tag)
            
            # Extract featured image
            featured_img = soup.select_one('.featured-image img, .post-thumbnail img, .entry-thumbnail img')
            if featured_img and featured_img.get('src'):
                img_url = urljoin(self.base_url, featured_img['src'])
                filename = self.download_media(img_url)
                if filename:
                    post_data['featured_image'] = filename
            
            # Extract other media files
            if post_data['content']:
                content_soup = BeautifulSoup(post_data['content'], 'html.parser')
                images = content_soup.find_all('img')
                for img in images:
                    src = img.get('src')
                    if src and not src.startswith('data:'):
                        full_url = urljoin(self.base_url, src)
                        filename = self.download_media(full_url)
                        if filename:
                            post_data['media_files'].append(filename)
            
            # Save individual post
            safe_title = re.sub(r'[^\w\s-]', '', post_data['title']).strip()
            safe_title = re.sub(r'[-\s]+', '-', safe_title)
            post_filename = f"post_{safe_title or 'unnamed'}_{int(time.time())}.json"
            post_file = os.path.join(self.posts_dir, post_filename)
            
            with open(post_file, 'w', encoding='utf-8') as f:
                json.dump(post_data, f, indent=2, ensure_ascii=False)
            
            return post_data
            
        except Exception as e:
            print(f"Error scraping post {post_url}: {e}")
            return None
    
    def scrape_events(self):
        """Scrape events from the website"""
        print("Scraping events...")
        
        # Try common events URL patterns
        events_urls = [
            urljoin(self.base_url, '/events/'),
            urljoin(self.base_url, '/calendar/'),
            urljoin(self.base_url, '/category/events/'),
        ]
        
        events_data = []
        
        for events_url in events_urls:
            soup = self.get_page_content(events_url)
            if not soup:
                continue
            
            # Find event links
            event_links = []
            link_selectors = [
                'a[href*="/event/"]',
                '.event-title a',
                '.event-item a',
                '.calendar-event a'
            ]
            
            for selector in link_selectors:
                links = soup.select(selector)
                for link in links:
                    href = link.get('href')
                    if href:
                        full_url = urljoin(self.base_url, href)
                        if full_url not in self.scraped_urls:
                            event_links.append(full_url)
            
            # Scrape each event
            for event_url in event_links[:10]:  # Limit to first 10 events
                event_data = self.scrape_single_event(event_url)
                if event_data:
                    events_data.append(event_data)
                    self.scraped_urls.add(event_url)
                time.sleep(1)
        
        # Save all events
        events_file = os.path.join(self.events_dir, 'all_events.json')
        with open(events_file, 'w', encoding='utf-8') as f:
            json.dump(events_data, f, indent=2, ensure_ascii=False)
        
        print(f"Scraped {len(events_data)} events")
        return events_data
    
    def scrape_single_event(self, event_url):
        """Scrape a single event"""
        try:
            soup = self.get_page_content(event_url)
            if not soup:
                return None
            
            event_data = {
                'url': event_url,
                'title': '',
                'description': '',
                'date': '',
                'time': '',
                'location': '',
                'venue': '',
                'price': '',
                'registration_url': '',
                'image': '',
                'metadata': {},
                'scraped_at': datetime.now().isoformat()
            }
            
            # Extract title
            title_selectors = [
                'h1.entry-title',
                'h1.event-title',
                'h1'
            ]
            
            for selector in title_selectors:
                title_elem = soup.select_one(selector)
                if title_elem:
                    event_data['title'] = self.clean_text(title_elem.get_text())
                    break
            
            # Extract description
            desc_selectors = [
                '.event-description',
                '.entry-content',
                '.event-content'
            ]
            
            for selector in desc_selectors:
                desc_elem = soup.select_one(selector)
                if desc_elem:
                    event_data['description'] = self.h.handle(str(desc_elem))
                    break
            
            # Extract date/time
            datetime_selectors = [
                '.event-date',
                '.event-time',
                '.event-datetime',
                'time'
            ]
            
            for selector in datetime_selectors:
                datetime_elem = soup.select_one(selector)
                if datetime_elem:
                    datetime_text = self.clean_text(datetime_elem.get_text())
                    if 'date' in selector or datetime_elem.get('datetime'):
                        event_data['date'] = datetime_elem.get('datetime', '') or datetime_text
                    else:
                        event_data['time'] = datetime_text
            
            # Extract location
            location_selectors = [
                '.event-location',
                '.event-venue',
                '.location'
            ]
            
            for selector in location_selectors:
                location_elem = soup.select_one(selector)
                if location_elem:
                    location_text = self.clean_text(location_elem.get_text())
                    if 'venue' in selector:
                        event_data['venue'] = location_text
                    else:
                        event_data['location'] = location_text
            
            # Extract registration link
            reg_selectors = [
                'a[href*="register"]',
                '.register-button a',
                '.event-registration a'
            ]
            
            for selector in reg_selectors:
                reg_elem = soup.select_one(selector)
                if reg_elem and reg_elem.get('href'):
                    event_data['registration_url'] = urljoin(self.base_url, reg_elem['href'])
                    break
            
            # Extract featured image
            featured_img = soup.select_one('.event-image img, .featured-image img')
            if featured_img and featured_img.get('src'):
                img_url = urljoin(self.base_url, featured_img['src'])
                filename = self.download_media(img_url)
                if filename:
                    event_data['image'] = filename
            
            # Extract metadata
            event_data['metadata'] = self.extract_page_metadata(soup)
            
            # Save individual event
            safe_title = re.sub(r'[^\w\s-]', '', event_data['title']).strip()
            safe_title = re.sub(r'[-\s]+', '-', safe_title)
            event_filename = f"event_{safe_title or 'unnamed'}_{int(time.time())}.json"
            event_file = os.path.join(self.events_dir, event_filename)
            
            with open(event_file, 'w', encoding='utf-8') as f:
                json.dump(event_data, f, indent=2, ensure_ascii=False)
            
            return event_data
            
        except Exception as e:
            print(f"Error scraping event {event_url}: {e}")
            return None
    
    def generate_migration_summary(self):
        """Generate a summary of scraped content for Laravel migration"""
        summary = {
            'scraping_date': datetime.now().isoformat(),
            'base_url': self.base_url,
            'content_summary': {
                'pages': 0,
                'posts': 0,
                'events': 0,
                'media_files': 0
            },
            'directory_structure': {
                'pages': self.pages_dir,
                'posts': self.posts_dir,
                'events': self.events_dir,
                'media': self.media_dir
            },
            'laravel_migration_notes': {
                'posts_table': 'Use posts_data for blog/news content',
                'events_table': 'Use events_data for calendar events',
                'media_table': 'Store media files in storage/app/public/media',
                'pages_table': 'Use pages_data for static pages',
                'categories': 'Extract from post categories and tags',
                'users': 'Authors from posts can be users table'
            },
            'next_steps': [
                'Review scraped content for quality',
                'Map WordPress categories to Laravel categories',
                'Set up media storage in Laravel',
                'Create Laravel seeders from JSON data',
                'Test content display in new Laravel site'
            ]
        }
        
        # Count files
        if os.path.exists(self.pages_dir):
            summary['content_summary']['pages'] = len([f for f in os.listdir(self.pages_dir) if f.endswith('.json')])
        
        if os.path.exists(self.posts_dir):
            summary['content_summary']['posts'] = len([f for f in os.listdir(self.posts_dir) if f.endswith('.json')])
        
        if os.path.exists(self.events_dir):
            summary['content_summary']['events'] = len([f for f in os.listdir(self.events_dir) if f.endswith('.json')])
        
        if os.path.exists(self.media_dir):
            summary['content_summary']['media_files'] = len([f for f in os.listdir(self.media_dir)])
        
        # Save summary
        summary_file = os.path.join(self.base_dir, 'migration_summary.json')
        with open(summary_file, 'w', encoding='utf-8') as f:
            json.dump(summary, f, indent=2, ensure_ascii=False)
        
        print(f"Migration summary saved to {summary_file}")
        return summary
    
    def run_full_scrape(self):
        """Run the complete scraping process"""
        print("Starting full website scrape...")
        
        # Step 1: Scrape main page
        homepage_data = self.scrape_main_page()
        
        # Step 2: Discover all pages
        discovered_pages = self.discover_pages()
        print(f"Discovered {len(discovered_pages)} pages to scrape")
        
        # Step 3: Scrape additional pages (excluding homepage)
        for page_url in discovered_pages[1:10]:  # Limit to first 9 additional pages
            if page_url not in self.scraped_urls:
                try:
                    soup = self.get_page_content(page_url)
                    if soup:
                        page_data = {
                            'url': page_url,
                            'title': soup.find('title').get_text() if soup.find('title') else '',
                            'content': self.h.handle(str(soup)),
                            'metadata': self.extract_page_metadata(soup),
                            'scraped_at': datetime.now().isoformat()
                        }
                        
                        # Save page
                        safe_title = re.sub(r'[^\w\s-]', '', page_data['title']).strip()
                        safe_title = re.sub(r'[-\s]+', '-', safe_title)
                        filename = f"page_{safe_title or 'unnamed'}_{int(time.time())}.json"
                        page_file = os.path.join(self.pages_dir, filename)
                        
                        with open(page_file, 'w', encoding='utf-8') as f:
                            json.dump(page_data, f, indent=2, ensure_ascii=False)
                        
                        self.scraped_urls.add(page_url)
                        
                except Exception as e:
                    print(f"Error scraping page {page_url}: {e}")
                
                time.sleep(1)
        
        # Step 4: Scrape posts
        posts_data = self.scrape_posts()
        
        # Step 5: Scrape events
        events_data = self.scrape_events()
        
        # Step 6: Generate migration summary
        summary = self.generate_migration_summary()
        
        print("\n" + "="*50)
        print("SCRAPING COMPLETE")
        print("="*50)
        print(f"Pages scraped: {summary['content_summary']['pages']}")
        print(f"Posts scraped: {summary['content_summary']['posts']}")
        print(f"Events scraped: {summary['content_summary']['events']}")
        print(f"Media files downloaded: {summary['content_summary']['media_files']}")
        print(f"Content saved in: {self.base_dir}")
        print("="*50)
        
        return summary

def main():
    """Main entry point"""
    scraper = WebsiteScraper()
    scraper.run_full_scrape()

if __name__ == "__main__":
    main()
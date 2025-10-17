'use client';

import { useEffect, useState } from 'react';
import Image from 'next/image';
import Button from '@/components/ui/Button';
import { getBlogData } from '@/lib/api';
import { BlogData } from '@/types/api';

const defaultBlogPosts = [
  {
    id: 1,
    title: 'Autonomous transport system to position...',
    description: '',
  },
  {
    id: 2,
    title: 'Autonomous transport system to position...',
    description: '',
  },
  {
    id: 3,
    title: 'Autonomous transport system to position...',
    description: '',
  },
];

export default function TravelBlog() {
  const [blogData, setBlogData] = useState<BlogData[]>(defaultBlogPosts);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function fetchData() {
      const data = await getBlogData();
      if (data && data.length > 0) {
        setBlogData(data.slice(0, 3));
      }
      setLoading(false);
    }
    fetchData();
  }, []);

  const displayPosts = blogData.length > 0 ? blogData : defaultBlogPosts;

  return (
    <section className="py-16 md:py-32 px-6 md:px-32 relative overflow-hidden">
      {/* Header */}
      <div className="text-center mb-12 md:mb-16">
        <h2 className="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 mb-4">
          {loading ? 'Loading...' : 'News and Updates'}
        </h2>
        <p className="text-base md:text-lg text-gray-600">
          {loading
            ? 'Loading...'
            : 'What our customers have to say about our fleet and services.'}
        </p>
      </div>

      {/* Blog Post Cards */}
      <div className="flex flex-col md:flex-row justify-center gap-5 mb-8 mt-8 md:mt-16">
        {displayPosts.map((post, index) => (
          <div
            key={post.id}
            className="bg-white rounded-lg shadow-lg overflow-hidden w-full md:w-85 h-72 md:h-80 flex flex-col"
          >
            {/* Blog Image */}
            <div className="relative h-48 w-full bg-gray-200">
              <Image
                src={`/images/travel${index + 1}.png`}
                alt={post.title}
                fill
                className="object-cover"
              />
            </div>

            {/* Content */}
            <div className="p-6 flex flex-col justify-between flex-1">
              {/* Title */}
              <h3 className="font-semibold text-gray-800 text-center text-lg mb-4 leading-tight">
                {post.title}
              </h3>

              {/* Read More Link */}
              <div className="text-center">
                <span className="text-[#00B935] font-medium cursor-pointer hover:text-[#00B935]/80 transition-colors">
                  Read more
                </span>
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Central Read More Button */}
      <div className="text-center mt-16 md:mt-32">
        <Button
          variant="primary"
          size="md"
          className="bg-[#00B935] hover:bg-[#00B935]/90 text-white font-semibold px-10 py-6 rounded-lg"
        >
          Read More
        </Button>
      </div>
      <div className="absolute -bottom-[800px] left-0 right-0 -z-10 hidden md:block">
        <Image
          src="/images/updates-bg.png"
          alt="Travel Blog Background"
          width={1000}
          height={1000}
          className="object-bottom w-full"
        />
      </div>
    </section>
  );
}

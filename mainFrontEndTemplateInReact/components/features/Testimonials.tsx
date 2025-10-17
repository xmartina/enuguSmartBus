'use client';

import { useEffect, useState } from 'react';
import Image from 'next/image';
import Button from '@/components/ui/Button';
import { getTestimonialHeader, getTestimonials } from '@/lib/api';
import { TestimonialHeader, Testimonial } from '@/types/api';

const defaultTestimonials = [
  {
    id: 1,
    name: 'Dr. James Wilson',
    designation: 'Civil Servant',
    image: '/images/test1.png',
    comment:
      'Comfortable buses, neat, spacious, experienced and organized drivers.',
    serial: 1,
  },
  {
    id: 2,
    name: 'Dr. Sarah Chen',
    designation: 'CEO, Healthcare Group',
    image: '/images/test2.png',
    comment:
      'Comfortable buses, neat, spacious, experienced and organized drivers.',
    serial: 2,
  },
  {
    id: 3,
    name: 'Mrs Sarah Okoro',
    designation: 'CEO, Healthcare Group',
    image: '/images/test3.png',
    comment:
      'Comfortable buses, neat, spacious, experienced and organized drivers.',
    serial: 3,
  },
];

export default function Testimonials() {
  const [headerData, setHeaderData] = useState<TestimonialHeader | null>(null);
  const [testimonials, setTestimonials] =
    useState<Testimonial[]>(defaultTestimonials);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function fetchData() {
      const [header, data] = await Promise.all([
        getTestimonialHeader(),
        getTestimonials(),
      ]);

      if (header && header.length > 0) {
        setHeaderData(header[0]);
      }

      if (data && data.length > 0) {
        setTestimonials(data.sort((a, b) => a.serial - b.serial).slice(0, 3));
      }

      setLoading(false);
    }
    fetchData();
  }, []);

  const defaultHeader = {
    title: 'Customer Testimonials',
    description: 'What our customers have to say about our fleet and services.',
  };

  const displayHeader = headerData || defaultHeader;

  return (
    <section className="py-16 md:py-32 px-6 md:px-36 relative overflow-hidden">
      {/* Header */}
      <div className="text-center mb-12 md:mb-16">
        <h2 className="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 mb-4">
          {loading ? 'Loading...' : displayHeader.title}
        </h2>
        <p className="text-base md:text-lg text-gray-600">
          {loading ? 'Loading...' : displayHeader.description}
        </p>
      </div>

      {/* Testimonial Cards */}
      <div className="flex flex-col md:flex-row justify-center gap-4 md:gap-8 mb-8">
        {testimonials.map((testimonial, index) => (
          <div
            key={testimonial.id}
            className="bg-white rounded-lg shadow-lg p-6 w-full md:w-80 h-64 md:h-70 flex flex-col justify-between"
          >
            {/* Profile Image */}
            <div className="flex justify-center mb-4">
              {testimonial.image.startsWith('http') ? (
                <img
                  src={testimonial.image}
                  alt={testimonial.name}
                  className="w-20 h-20 rounded-full object-cover"
                />
              ) : (
                <Image
                  src={`/images/test${index + 1}.png`}
                  alt={testimonial.name}
                  width={80}
                  height={80}
                  className="rounded-full object-cover"
                />
              )}
            </div>

            {/* Name and Title */}
            <div className="text-center">
              <h3 className="font-bold text-gray-800 text-lg">
                {testimonial.name}
              </h3>
              <p className="text-gray-600 text-sm">{testimonial.designation}</p>
            </div>

            {/* Quote */}
            <p className="text-gray-700 text-center text-sm leading-relaxed">
              "{testimonial.comment}"
            </p>
          </div>
        ))}
      </div>

      {/* Read More Button */}
      <div className="text-center mt-16 md:mt-28">
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
          src="/images/customer-bg.png"
          alt="Customer Background"
          width={1000}
          height={1000}
          className="object-bottom w-full"
        />
      </div>
    </section>
  );
}

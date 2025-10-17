'use client';

import { useEffect, useState } from 'react';
import Image from 'next/image';
import Button from '@/components/ui/Button';
import { getHeroData } from '@/lib/api';
import { HeroData } from '@/types/api';

export default function Hero() {
  const [heroData, setHeroData] = useState<HeroData | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function fetchData() {
      const data = await getHeroData();
      if (data && data.length > 0) {
        setHeroData(data[0]);
      }
      setLoading(false);
    }
    fetchData();
  }, []);

  if (loading) {
    return (
      <section className="py-10 px-6 md:px-32 relative min-h-screen flex items-center justify-center">
        <div className="text-white text-xl">Loading...</div>
      </section>
    );
  }

  const defaultData = {
    title: 'Welcome to Enugu Smart Bus',
    sub_title: 'Smart. Safe. Seamless Mobility for Everyone in Enugu State.',
    image: '/images/hero-bus.png',
    button_text: 'Learn More',
  };

  const displayData = heroData || defaultData;

  return (
    <section className="py-10 px-6 md:px-32 relative min-h-screen flex items-center">
      {/* Background Image */}
      <div className="absolute inset-0 z-0">
        <Image
          src="/images/hero-bus.jpg"
          alt="Bus Depot Background"
          fill
          className="object-cover blur-xs"
        />
        <div className="absolute inset-0 bg-[#00131AC4]"></div>
      </div>

      <div className="relative z-10 flex flex-col lg:flex-row items-start gap-16 w-full max-w-full pt-12">
        {/* Left Section - Text Content */}
        <div className="flex-1 text-white">
          <h1 className="text-3xl md:text-4xl lg:text-5xl font-bold mb-6 leading-tight">
            {displayData.title}
          </h1>

          <p className="text-lg md:text-xl mb-8 text-gray-200 leading-relaxed">
            {displayData.sub_title}
          </p>

          <div className="space-y-4">
            <Button
              variant="primary"
              size="lg"
              className="bg-[#00B935] hover:bg-[#00B935]/90 text-white px-8 py-4 rounded-lg font-semibold text-lg"
            >
              {displayData.button_text}
            </Button>

            <div className="text-white text-lg">Download our mobile app</div>
          </div>
        </div>

        {/* Right Section - Bus Image */}
        <div className="flex-1 flex justify-center w-full lg:w-auto">
          <div className="relative w-full max-w-lg">
            <div className="rounded-2xl p-2">
              {heroData?.image ? (
                <img
                  src={heroData.image}
                  alt="Enugu Smart Bus"
                  className="rounded-xl object-cover w-full h-full z-10"
                />
              ) : (
                <Image
                  src={displayData.image}
                  alt="Enugu Smart Bus"
                  width={600}
                  height={400}
                  className="rounded-xl object-cover w-full h-full z-10"
                />
              )}
            </div>
            <div className="absolute inset-0 bg-gradient-to-br from-[#00B935] to-[#195AFF] rounded-2xl p-1 left-10 -top-10 w-[95%] h-[95%] -z-10"></div>
          </div>
        </div>
      </div>
    </section>
  );
}

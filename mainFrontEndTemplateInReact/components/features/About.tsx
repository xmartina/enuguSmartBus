'use client';

import { useEffect, useState } from 'react';
import Image from 'next/image';
import Button from '@/components/ui/Button';
import { getJourneyData } from '@/lib/api';
import { JourneyData } from '@/types/api';

export default function About() {
  const [journeyData, setJourneyData] = useState<JourneyData | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function fetchData() {
      const data = await getJourneyData();
      if (data && data.length > 0) {
        setJourneyData(data[0]);
      }
      setLoading(false);
    }
    fetchData();
  }, []);

  const defaultData = {
    title: 'About Enugu Smart Bus',
    description:
      'Enugu Smart Bus is a modern public transport system that combines comfort, safety, and technology to transform the way people move across Enugu State. Our eco-friendly CNG and hybrid buses are equipped with real-time GPS tracking, AI-powered route optimization, on-board Wi-Fi, digital ticketing, and intelligent safety systems — ensuring a smarter, greener, and more convenient journey for all.',
  };

  const displayData = journeyData || defaultData;

  return (
    <section className="py-16 md:py-32 px-6 md:px-32 relative">
      {/* Background Bus Image */}
      <div className="absolute inset-0 z-0">
        <Image
          src="/images/blue.png"
          alt="Bus Background"
          fill
          className="object-cover opacity-10"
        />
      </div>

      <div className="relative z-10 flex flex-col lg:flex-row items-center gap-10">
        {/* Left Section - Governor Portrait */}
        <div className="flex-1 flex justify-center overflow-visible w-full lg:w-auto">
          <div className="relative w-80 md:w-96 h-[400px] md:h-[500px]">
            {/* Governor Info */}
            <div className="absolute -bottom-12 -left-12 right-10 bg-[#195AFFD9] text-white p-1 rounded-lg text-center z-10 w-64 md:w-80 h-full">
              <div className="flex flex-col justify-end h-full">
                <h3 className="text-xl">Governor Peter Mbah</h3>
                <p className="text-sm opacity-90 font-semibold">
                  Governor of Enugu State
                </p>
              </div>
            </div>

            {/* Image - On top of the info card */}
            <div className="relative bg-white rounded-lg p-4 h-full z-20">
              <Image
                src="/images/peter.png"
                alt="Governor Peter Mbah"
                fill
                className="object-cover rounded-lg"
              />
            </div>
          </div>
        </div>

        {/* Right Section - About Content */}
        <div className="flex-1 flex flex-col justify-center mt-8 lg:mt-0">
          <h2 className="text-2xl md:text-3xl lg:text-4xl font-bold text-primary mb-6">
            {loading ? 'Loading...' : displayData.title}
          </h2>

          <p className="text-base md:text-lg text-gray-600 leading-relaxed mb-8">
            {loading ? 'Loading content...' : displayData.description}
          </p>

          <Button
            variant="primary"
            size="lg"
            className="bg-[#00A424] hover:bg-[#00A424]/90 text-white px-8 py-3 rounded-lg font-semibold self-start"
          >
            Read More
          </Button>
        </div>
      </div>
    </section>
  );
}

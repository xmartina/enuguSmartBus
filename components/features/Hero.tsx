'use client';

import Image from 'next/image';
import Button from '@/components/ui/Button';

export default function Hero() {
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
            Welcome to Enugu Smart Bus
          </h1>

          <p className="text-lg md:text-xl mb-8 text-gray-200 leading-relaxed">
            Smart. Safe. Seamless Mobility for Everyone in Enugu State.
          </p>

          <div className="space-y-4">
            <Button
              variant="primary"
              size="lg"
              className="bg-[#00B935] hover:bg-[#00B935]/90 text-white px-8 py-4 rounded-lg font-semibold text-lg"
            >
              Learn More
            </Button>

            <div className="text-white text-lg">Download our mobile app</div>
          </div>
        </div>

        {/* Right Section - Bus Image */}
        <div className="flex-1 flex justify-center w-full lg:w-auto">
          <div className="relative w-full max-w-lg">
            <div className="rounded-2xl p-2">
              <Image
                src="/images/hero-bus.png"
                alt="Enugu Smart Bus"
                width={600}
                height={400}
                className="rounded-xl object-cover w-full h-full z-10"
              />
            </div>
            <div className="absolute inset-0 bg-gradient-to-br from-[#00B935] to-[#195AFF] rounded-2xl p-1 left-10 -top-10 w-[95%] h-[95%] -z-10"></div>
          </div>
        </div>
      </div>
    </section>
  );
}

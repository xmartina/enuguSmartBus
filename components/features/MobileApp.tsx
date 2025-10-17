'use client';

import Image from 'next/image';
import Button from '@/components/ui/Button';

export default function MobileApp() {
  return (
    <section className="py-16 md:py-32 px-6 md:px-32 relative">
      <div className="flex flex-col lg:flex-row items-center gap-10">
        <div className="absolute left-20 top-40 hidden md:block">
          <Image
            src="/images/mobile-bg.png"
            alt="Mobile App Preview"
            width={500}
            height={500}
          />
        </div>
        {/* Left Content */}
        <div className="flex-1">
          <h2 className="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 mb-6">
            Download Our Mobile App
          </h2>

          <p className="text-base md:text-lg font-semibold mb-2 leading-relaxed">
            Enugu's leading tour and travels Booking website. Book travel
            packages and enjoy your holidays with distinctive experience.
          </p>

          {/* Features List */}
          <ul className="space-y-3 mb-10 ml-8 font-semibold">
            <li className="flex items-center text-gray-700">
              <span className="w-1.5 h-1.5 bg-black rounded-full mr-3"></span>
              Smart Card ticketing System
            </li>
            <li className="flex items-center text-gray-700">
              <span className="w-1.5 h-1.5 bg-black rounded-full mr-3"></span>
              Reduce time wastage on travel Booking
            </li>
            <li className="flex items-center text-gray-700">
              <span className="w-1.5 h-1.5 bg-black rounded-full mr-3"></span>
              Leading tour and travels Booking
            </li>
          </ul>

          {/* Download Buttons */}
          <div className="flex flex-col sm:flex-row gap-4 mt-5">
            {/* App Store Button */}
            <Button
              variant="outline"
              size="lg"
              className="bg-black hover:bg-gray-800 text-white px-6 py-4 rounded-lg flex items-center gap-3 min-w-[150px]"
            >
              <Image
                src="/images/apple.svg"
                alt="App Store"
                width={16}
                height={16}
                className="flex-shrink-0"
              />
              <div className="flex flex-col items-start">
                <span className="text-xs text-gray-300">Download on the</span>
                <span className="font-semibold">App Store</span>
              </div>
            </Button>

            {/* Google Play Button */}
            <Button
              variant="outline"
              size="lg"
              className="bg-black hover:bg-gray-800 text-white px-6 py-4 rounded-lg flex items-center gap-3 min-w-[150px]"
            >
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="currentColor"
                className="flex-shrink-0"
              >
                <path d="M3,20.5V3.5C3,2.91 3.34,2.39 3.84,2.15L13.69,12L3.84,21.85C3.34,21.6 3,21.09 3,20.5M16.81,15.12L6.05,21.34L14.54,12.85L16.81,15.12M20.16,10.81C20.5,11.08 20.5,11.57 20.16,11.84L17.19,13.54L14.54,10.89L17.19,8.24L20.16,10.81M6.05,2.66L16.81,8.88L14.54,11.15L6.05,2.66Z" />
              </svg>
              <div className="flex flex-col items-start">
                <span className="text-xs text-gray-300">GET IT ON</span>
                <span className="text-lg font-semibold">Google Play</span>
              </div>
            </Button>
          </div>
        </div>

        {/* Right Image */}
        <div className="flex-1 flex justify-center w-full lg:w-auto">
          <div className="relative w-80 md:w-96 h-[500px] md:h-[700px]">
            <Image
              src="/images/mobile.png"
              alt="Mobile App Preview"
              fill
              className="object-contain"
            />
          </div>
        </div>
      </div>
      <div className="absolute bottom-0 left-0 right-0 hidden md:block">
        <Image
          src="/images/app-bg.png"
          alt="Mobile App Preview"
          width={1000}
          height={1000}
          className="object-cover w-full h-full"
        />
      </div>
    </section>
  );
}

'use client';

import { Mail } from 'lucide-react';
import Image from 'next/image';

export default function Newsletter() {
  return (
    <section className="py-16 md:py-42 flex items-center px-6 md:px-42 relative">
      {/* Bottom Image - Positioned at the bottom */}
      <Image
        src="/images/news-bg.png"
        alt="Newsletter Background"
        width={1200}
        height={400}
        className="absolute bottom-[-500px] left-0 right-0 w-[120vw] h-[800px]"
      />

      {/* Content */}
      <div className="relative z-10 flex flex-col lg:flex-row items-center w-full gap-8">
        <div className="font-poppins flex-1 text-center lg:text-left">
          <h2 className="text-2xl md:text-3xl lg:text-4xl font-semibold">
            Subscribe to Our Newsletter for news, Tips and Updates
          </h2>
          <p className="text-gray-600 pt-2 text-base md:text-lg">
            Subscribe and be the first to hear about our offers
          </p>
        </div>

        <div
          className="rounded-3xl p-0.5 lg:ml-10 w-full lg:w-auto"
          style={{
            background:
              'linear-gradient(90deg, #33C29E 0%, rgba(25, 90, 255, 0.63) 100%)',
          }}
        >
          <div className="bg-white rounded-3xl px-2 py-2 flex items-center gap-2 sm:gap-4">
            <div className="flex items-center flex-[3] min-w-0">
              <Mail className="h-5 w-5 sm:h-6 sm:w-6 text-black mr-2 sm:mr-3 flex-shrink-0" />
              <input
                type="email"
                placeholder="Enter your email"
                className="flex-1 px-2 sm:px-16 outline-none text-gray-700 placeholder-gray-500 placeholder:text-xs sm:placeholder:text-sm bg-transparent min-w-0 text-sm sm:text-base"
              />
            </div>
            <button className="bg-[#00B935] hover:bg-[#00B935]/90 text-white font-bold px-3 sm:px-5 py-2 rounded-xl transition-colors shadow-md whitespace-nowrap flex-shrink-0 text-xs sm:text-base">
              Subscribe
            </button>
          </div>
        </div>
      </div>
    </section>
  );
}

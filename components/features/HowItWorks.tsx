'use client';

import Image from 'next/image';

const steps = [
  {
    id: 1,
    title: 'Step 1: Register or Sign In',
    description:
      'Create your Smart Bus account through our website or mobile app to access all services.',
    image: '/images/step1.png',
  },
  {
    id: 2,
    title: 'Step 2: Book your trip',
    description:
      'Choose your preferred route, departure time, and destination, then confirm your booking online or through the app.',
    image: '/images/step2.png',
  },
  {
    id: 3,
    title: 'Step 3: Board and pay',
    description:
      'Scan your QR code or tap your Smart Card when boarding. Pay seamlessly using your wallet balance or linked payment method.',
    image: '/images/step3.png',
  },
  {
    id: 4,
    title: 'Step 4: Track and Enjoy',
    description:
      'Track your bus in real time, monitor arrival times, and enjoy a safe and comfortable ride across the city.',
    image: '/images/step4.png',
  },
];

export default function HowItWorks() {
  return (
    <section className="py-16 md:py-32 px-6 md:px-32 relative">
      {/* Header */}
      <div className="text-center mb-12 md:mb-24">
        <h2 className="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 mb-4">
          How Enugu Smart Bus Works
        </h2>
        <p className="text-base md:text-lg text-gray-600">
          Simple steps to enjoy a smarter way of moving around Enugu.
        </p>
      </div>

      <div className="absolute top-15 left-10 hidden md:block">
        <Image
          src="/images/smart.png"
          alt="How It Works"
          width={500}
          height={500}
          className=""
        />
      </div>

      {/* Steps Cards */}
      <div className="flex flex-col md:flex-row justify-center gap-4 mb-16 md:mb-32">
        {steps.map((step) => (
          <div
            key={step.id}
            className="bg-white rounded-lg shadow-lg overflow-hidden w-full md:w-80 h-80 md:h-96 flex flex-col"
          >
            {/* Step Image */}
            <div className="relative h-48 w-full">
              <Image
                src={step.image}
                alt={step.title}
                fill
                className="object-cover"
              />
            </div>

            {/* Content */}
            <div className="p-6 flex flex-col justify-between flex-1">
              {/* Title */}
              <h3 className="font-bold text-gray-800 text-lg mb-3 leading-tight">
                {step.title}
              </h3>

              {/* Description */}
              <p className="text-gray-600 text-sm leading-relaxed">
                {step.description}
              </p>
            </div>
          </div>
        ))}
      </div>
      <div className="absolute bottom-20 right-30 hidden md:block">
        <Image
          src="/images/bus.png"
          alt="How It Works"
          width={1500}
          height={500}
          className=""
        />
      </div>
    </section>
  );
}

'use client';

import { useEffect, useState, FormEvent } from 'react';
import { Mail } from 'lucide-react';
import Image from 'next/image';
import { getNewsletterData, subscribeNewsletter } from '@/lib/api';
import { NewsletterData } from '@/types/api';

export default function Newsletter() {
  const [newsletterData, setNewsletterData] = useState<NewsletterData | null>(
    null
  );
  const [email, setEmail] = useState('');
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [message, setMessage] = useState('');

  useEffect(() => {
    async function fetchData() {
      const data = await getNewsletterData();
      if (data && data.length > 0) {
        setNewsletterData(data[0]);
      }
      setLoading(false);
    }
    fetchData();
  }, []);

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    if (!email) return;

    setSubmitting(true);
    setMessage('');

    const success = await subscribeNewsletter(email);

    if (success) {
      setMessage('Thank you for subscribing!');
      setEmail('');
    } else {
      setMessage('Subscription failed. Please try again.');
    }

    setSubmitting(false);
    setTimeout(() => setMessage(''), 5000);
  };

  const defaultData = {
    title: 'Subscribe to Our Newsletter for news, Tips and Updates',
    sub_title: 'Subscribe and be the first to hear about our offers',
  };

  const displayData = newsletterData || defaultData;

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
            {loading ? 'Loading...' : displayData.title}
          </h2>
          <p className="text-gray-600 pt-2 text-base md:text-lg">
            {loading ? 'Loading...' : displayData.sub_title}
          </p>
        </div>

        <form onSubmit={handleSubmit} className="w-full lg:w-auto">
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
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  required
                  disabled={submitting}
                  className="flex-1 px-2 sm:px-16 outline-none text-gray-700 placeholder-gray-500 placeholder:text-xs sm:placeholder:text-sm bg-transparent min-w-0 text-sm sm:text-base"
                />
              </div>
              <button
                type="submit"
                disabled={submitting}
                className="bg-[#00B935] hover:bg-[#00B935]/90 text-white font-bold px-3 sm:px-5 py-2 rounded-xl transition-colors shadow-md whitespace-nowrap flex-shrink-0 text-xs sm:text-base disabled:opacity-50"
              >
                {submitting ? 'Subscribing...' : 'Subscribe'}
              </button>
            </div>
          </div>
          {message && (
            <p
              className={`mt-2 text-sm text-center ${
                message.includes('Thank you')
                  ? 'text-green-600'
                  : 'text-red-600'
              }`}
            >
              {message}
            </p>
          )}
        </form>
      </div>
    </section>
  );
}

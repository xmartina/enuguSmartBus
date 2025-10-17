import { ArrowRight } from 'lucide-react';
import Image from 'next/image';

export default function Footer() {
  const currentYear = new Date().getFullYear();

  return (
    <footer className="px-6 md:px-24 py-16 md:py-24 relative">
      {/* Footer Background Image */}
      <div className="absolute inset-0 w-full h-full">
        <Image
          src="/images/footer-bg.jpg"
          alt="Footer Background"
          fill
          className="object-cover opacity-10"
          style={{ objectPosition: 'center 45%' }}
        />
      </div>

      {/* Footer Content */}
      <div className="relative z-10">
        <aside className="footer-grid grid gap-8 md:gap-28 grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
          <div className="md:col-span-2 lg:col-span-1">
            <Image
              src="/images/footer.png"
              alt="Enugu Smart Bus Service"
              width={100}
              height={100}
              className="mb-4"
            />
            <p className="font-inter text-sm md:text-base">
              Lorem Ipsum is simply dummy text of the printing and typesetting
              industry. Lorem Ipsum has been the industry's standard dummy text
              ever since the 1500s, when an unknown printer took a galley of
              type and{' '}
            </p>
          </div>
          <div className="space-y-2">
            <h4 className="text-base md:text-lg font-bold text-[#002621] mb-4">
              Quick Link
            </h4>
            <p className="text-sm md:text-base">About</p>
            <button
              style={{
                background:
                  'linear-gradient(90deg, #33C29E 48.09%, rgba(25, 90, 255, 0.63) 113.66%)',
              }}
              className="px-4 md:px-7 py-2 rounded-md text-white flex items-center text-sm md:text-base"
            >
              <ArrowRight color="white" className="mr-2" />
              Contact
            </button>
            <p className="text-sm md:text-base">Pricing</p>
            <p className="text-sm md:text-base">Blog</p>
          </div>
          <div className="space-y-2">
            <h4 className="text-base md:text-lg font-bold text-[#002621] mb-4">
              Candidate
            </h4>
            <ul className="space-y-2">
              <li>
                <a
                  href="/browse-jobs"
                  className="text-gray-700 hover:text-primary transition-colors text-sm md:text-base"
                >
                  Browse Jobs
                </a>
              </li>
              <li>
                <a
                  href="/browse-employers"
                  className="text-gray-700 hover:text-primary transition-colors text-sm md:text-base"
                >
                  Browse Employers
                </a>
              </li>
              <li>
                <a
                  href="/candidate-dashboard"
                  className="text-gray-700 hover:text-primary transition-colors text-sm md:text-base"
                >
                  Candidate Dashboard
                </a>
              </li>
              <li>
                <a
                  href="/saved-jobs"
                  className="text-gray-700 hover:text-primary transition-colors text-sm md:text-base"
                >
                  Saved Jobs
                </a>
              </li>
            </ul>
          </div>
          <div className="space-y-2">
            <h4 className="text-base md:text-lg font-bold text-[#002621] mb-4">
              Employers
            </h4>
            <ul className="space-y-2">
              <li>
                <a
                  href="/post-a-job"
                  className="text-gray-700 hover:text-primary transition-colors text-sm md:text-base"
                >
                  Post a Job
                </a>
              </li>
              <li>
                <a
                  href="/browse-candidates"
                  className="text-gray-700 hover:text-primary transition-colors text-sm md:text-base"
                >
                  Browse Candidates
                </a>
              </li>
              <li>
                <a
                  href="/employers-dashboard"
                  className="text-gray-700 hover:text-primary transition-colors text-sm md:text-base"
                >
                  Employers Dashboard
                </a>
              </li>
              <li>
                <a
                  href="/applications"
                  className="text-gray-700 hover:text-primary transition-colors text-sm md:text-base"
                >
                  Applications
                </a>
              </li>
            </ul>
          </div>
        </aside>
        <p className="text-center font-semibold mt-16 md:mt-28 text-sm md:text-base">
          © Copyright 2025 Enugu Smart Bus Initiative
        </p>
      </div>
    </footer>
  );
}

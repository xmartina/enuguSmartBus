import { useEffect, useState } from 'react';

/**
 * Custom hook to check if component is mounted
 * Useful for preventing hydration mismatches
 * @returns Boolean indicating if the component is mounted
 */
export function useMounted(): boolean {
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
  }, []);

  return mounted;
}

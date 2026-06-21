"use client";

import { useEffect, useRef } from "react";

export default function useScrollReveal(rootMargin = "0px 0px -40px 0px") {
  const ref = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const el = ref.current;
    if (!el) return;

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("revealed");
            observer.unobserve(entry.target);
          }
        });
      },
      { rootMargin, threshold: 0.01 }
    );

    // Observe direct children with reveal-on-scroll class
    const targets = el.querySelectorAll(
      ".reveal-on-scroll"
    ) as NodeListOf<HTMLElement>;
    targets.forEach((t) => observer.observe(t));

    return () => {
      observer.disconnect();
    };
  }, [rootMargin]);

  return ref;
}
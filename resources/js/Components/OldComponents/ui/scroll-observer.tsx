"use client";

import React from "react";
import useScrollReveal from "@/lib/useScrollReveal";

export default function ScrollObserver({
  children,
  className = "",
}: {
  children: React.ReactNode;
  className?: string;
}) {
  const ref = useScrollReveal();

  return (
    <div ref={ref} className={className}>
      {children}
    </div>
  );
}
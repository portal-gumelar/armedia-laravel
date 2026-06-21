"use client";

import React from 'react';
import { motion } from 'framer-motion';

export default function ACRSection() {
  const benefits = [
    { text: "Mendapatkan Poin dari setiap pembayaran tagihan.", icon: "🪙", color: "from-amber-400 to-orange-500" },
    { text: "Bonus Poin untuk pembayaran tepat waktu.", icon: "⏱️", color: "from-emerald-400 to-teal-500" },
    { text: "Bonus Poin loyalitas bagi pelanggan yang aktif berlangganan dalam jangka panjang.", icon: "🤝", color: "from-blue-400 to-indigo-500" },
    { text: "Bonus Poin dari Program referensi pelanggan baru.", icon: "👥", color: "from-purple-400 to-fuchsia-500" },
    { text: "Poin dapat ditukarkan dengan berbagai hadiah dan voucher.", icon: "🎁", color: "from-pink-400 to-rose-500" },
    { text: "Kesempatan mengikuti Program Undian dan hadiah spesial Armedia.", icon: "🎉", color: "from-red-500 to-red-700" }
  ];

  const rewards = [
    { name: "Voucher BBM", emoji: "⛽" },
    { name: "Potongan tagihan internet", emoji: "🧾" },
    { name: "Pulsa", emoji: "📱" },
    { name: "Paket data", emoji: "🌐" },
    { name: "Merchandise eksklusif Armedia", emoji: "👕" },
    { name: "Program undian berhadiah", emoji: "🏆" }
  ];

  return (
    <section id="acr-section" className="relative mx-auto max-w-7xl px-4 sm:px-6 py-24 lg:px-8 border-t border-slate-100 scroll-mt-20 overflow-hidden bg-slate-50 rounded-3xl my-12 shadow-[inset_0_0_0_1px_rgba(0,0,0,0.05)]">
      
      {/* Dynamic Animated Background Elements */}
      <div className="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-red-600/10 blur-3xl animate-[pulse_6s_ease-in-out_infinite]"></div>
      <div className="absolute bottom-0 left-0 -ml-20 -mb-20 w-[30rem] h-[30rem] rounded-full bg-orange-500/10 blur-3xl animate-[pulse_8s_ease-in-out_infinite_reverse]"></div>
      
      {/* Subtle Grid Pattern Overlay */}
      <div className="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.03] pointer-events-none"></div>
      
      <div className="relative z-10 max-w-6xl mx-auto">
        <motion.div 
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="text-center mb-16"
        >
          <motion.span 
            whileHover={{ scale: 1.05 }}
            className="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2 text-xs font-black uppercase tracking-widest text-red-600 mb-6 shadow-sm shadow-red-100 border border-red-100"
          >
            <span className="relative flex h-2 w-2">
              <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
              <span className="relative inline-flex rounded-full h-2 w-2 bg-red-600"></span>
            </span>
            PROGRAM LOYALITAS PELANGGAN
          </motion.span>
          <h2 className="mt-2 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl md:text-6xl drop-shadow-sm">
            Armedia Community <span className="text-transparent bg-clip-text bg-gradient-to-r from-red-600 via-orange-500 to-red-500">Reward</span> (ACR)
          </h2>
          <p className="mt-6 text-base leading-relaxed text-slate-600 max-w-4xl mx-auto text-justify">
            Adalah program loyalitas pelanggan yang dirancang sebagai bentuk penghargaan kepada seluruh pelanggan setia Armedia. Melalui program ini, setiap pelanggan yang aktif berlangganan dan melakukan pembayaran tagihan akan mendapatkan poin reward yang dapat dikumpulkan dan ditukarkan dengan berbagai hadiah menarik.
          </p>
        </motion.div>

        <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 items-stretch">
          
          {/* Left Side - Info & Benefits Grid */}
          <div className="lg:col-span-7 flex flex-col gap-6">
            <motion.div 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5 }}
              className="text-slate-600 text-sm md:text-base leading-relaxed bg-white/60 p-6 rounded-2xl border border-slate-100/50 shadow-sm backdrop-blur-sm space-y-4"
            >
              <p className="text-justify">
                Semakin lama menjadi pelanggan dan semakin disiplin dalam melakukan pembayaran, semakin banyak poin yang akan diperoleh. Poin tersebut dapat ditukarkan dengan berbagai pilihan reward seperti Voucher BBM, Potongan tagihan internet, Pulsa, Paket data, Merchandise eksklusif Armedia, hingga kesempatan mengikuti Program undian berhadiah.
              </p>
              <p className="text-justify">
                Program ini hadir sebagai wujud komitmen Armedia untuk tidak hanya menyediakan layanan internet yang stabil dan terjangkau, tetapi juga memberikan nilai tambah bagi pelanggan melalui berbagai keuntungan yang dapat dinikmati secara langsung.
              </p>
            </motion.div>
            
            <motion.div 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5 }}
              className="mt-6 mb-4"
            >
              <div className="inline-flex items-center gap-3 px-5 py-2.5 rounded-2xl bg-gradient-to-r from-slate-900 to-slate-800 text-white shadow-lg shadow-slate-900/20 border border-slate-700">
                <span className="text-xl">🏆</span>
                <h3 className="text-xs font-black tracking-widest uppercase">Keuntungan Member ACR</h3>
              </div>
            </motion.div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {benefits.map((benefit, index) => (
                <motion.div 
                  key={index}
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true }}
                  transition={{ duration: 0.5, delay: index * 0.05 }}
                  whileHover={{ y: -4, scale: 1.02 }}
                  className="bg-white/80 backdrop-blur-md rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-xl hover:shadow-slate-200 hover:border-slate-300 transition-all duration-500 relative overflow-hidden group cursor-default"
                >
                  <div className={`absolute inset-0 bg-gradient-to-br ${benefit.color} opacity-0 group-hover:opacity-[0.03] transition-opacity duration-500`}></div>
                  
                  <div className="flex items-start gap-4 relative z-10">
                    <div className={`flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br ${benefit.color} flex items-center justify-center text-2xl shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500`}>
                      <span className="drop-shadow-md">{benefit.icon}</span>
                    </div>
                    <div className="flex-1 flex items-center h-12">
                      <p className="text-slate-700 text-sm font-bold leading-snug group-hover:text-slate-900 transition-colors">
                        {benefit.text}
                      </p>
                    </div>
                  </div>
                </motion.div>
              ))}
            </div>

            <motion.div 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5 }}
              className="mt-4 p-5 bg-gradient-to-r from-red-50 to-orange-50 rounded-2xl border border-red-100/50 text-slate-700 text-sm leading-relaxed italic"
            >
              "Dengan Armedia Community Reward , setiap pembayaran tagihan bukan hanya untuk menikmati layanan internet, tetapi juga menjadi poin Investasi yang dapat memberikan berbagai manfaat dan keuntungan tambahan bagi pelanggan."
            </motion.div>
          </div>

          {/* Right Side - Visuals & Rewards */}
          <div className="lg:col-span-5 flex flex-col gap-6">
            {/* Promo Cards */}
            <div className="grid grid-cols-2 gap-4 h-48">
              <motion.div 
                initial={{ opacity: 0, scale: 0.9 }}
                whileInView={{ opacity: 1, scale: 1 }}
                viewport={{ once: true }}
                transition={{ duration: 0.5 }}
                whileHover={{ scale: 1.05, rotate: -2 }}
                className="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 text-white shadow-xl shadow-slate-900/20 flex flex-col justify-between relative overflow-hidden group"
              >
                <div className="absolute -right-4 -bottom-4 text-7xl opacity-20 group-hover:scale-110 transition-transform duration-500">💰</div>
                <div className="relative z-10">
                  <div className="w-10 h-10 bg-white/10 backdrop-blur-md rounded-full flex items-center justify-center text-xl mb-4 shadow-inner">
                    🎁
                  </div>
                  <div className="text-[10px] font-bold uppercase tracking-widest text-slate-600">Kumpulkan</div>
                  <div className="text-xl font-black mt-1 leading-tight text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-300">Poin<br/>Reward</div>
                </div>
              </motion.div>
              
              <motion.div 
                initial={{ opacity: 0, scale: 0.9 }}
                whileInView={{ opacity: 1, scale: 1 }}
                viewport={{ once: true }}
                transition={{ duration: 0.5, delay: 0.1 }}
                whileHover={{ scale: 1.05, rotate: 2 }}
                className="bg-gradient-to-br from-red-600 to-orange-500 rounded-3xl p-6 text-white shadow-xl shadow-red-600/20 flex flex-col justify-between relative overflow-hidden group"
              >
                <div className="absolute -right-4 -bottom-4 text-7xl opacity-20 group-hover:scale-110 transition-transform duration-500">💎</div>
                <div className="relative z-10">
                  <div className="w-10 h-10 bg-black/10 backdrop-blur-md rounded-full flex items-center justify-center text-xl mb-4 shadow-inner">
                    💎
                  </div>
                  <div className="text-[10px] font-bold uppercase tracking-widest text-red-200">Tukarkan dengan</div>
                  <div className="text-xl font-black mt-1 leading-tight text-white drop-shadow-sm">Hadiah &<br/>Voucher</div>
                </div>
              </motion.div>
            </div>

            {/* Rewards List Flex */}
            <motion.div 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5 }}
              className="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex-grow flex flex-col justify-center"
            >
              <h3 className="text-xs font-bold uppercase tracking-widest text-slate-600 mb-5 text-center flex items-center justify-center gap-2">
                <span className="w-8 h-px bg-slate-200"></span>
                Pilihan Reward Anda
                <span className="w-8 h-px bg-slate-200"></span>
              </h3>
              <div className="flex flex-wrap justify-center gap-2">
                {rewards.map((reward, index) => (
                  <motion.div 
                    key={index}
                    whileHover={{ scale: 1.1, y: -2 }}
                    className="inline-flex items-center gap-2 rounded-full bg-slate-50 px-4 py-2 text-sm font-bold text-slate-700 border border-slate-200 hover:border-red-400 hover:bg-red-50 hover:text-red-700 hover:shadow-md transition-all cursor-default"
                  >
                    <span>{reward.emoji}</span>
                    {reward.name}
                  </motion.div>
                ))}
              </div>
            </motion.div>

            {/* Launch Banner & Footer */}
            <motion.div 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5 }}
              whileHover={{ scale: 1.02 }}
              className="mt-2 bg-slate-900 rounded-2xl p-1 shadow-lg relative overflow-hidden"
            >
              <div className="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
              <div className="bg-white/5 backdrop-blur-sm rounded-xl py-6 px-6 text-center border border-white/10 relative z-10 flex flex-col items-center gap-4">
                
                <h3 className="text-sm font-black text-red-400 uppercase tracking-widest">
                  Komunitas Armedia Reward
                </h3>

                <p className="text-xs font-black text-white tracking-wide uppercase leading-relaxed">
                  Internet Tetap Terhubung,<br/>
                  Poin Terus Bertumbuh,<br/>
                  <span className="text-amber-400">Hadiah Menanti Anda.</span>
                </p>

                <div className="inline-block bg-red-600 text-white text-[10px] sm:text-xs font-black px-6 py-2.5 rounded-full uppercase tracking-widest animate-bounce shadow-lg shadow-red-600/30">
                  Tunggu tanggal Lounchingnya
                </div>

                <div className="mt-2 pt-4 border-t border-white/10 w-full text-[10px] font-medium text-slate-600 uppercase tracking-widest flex flex-col gap-1">
                  <span>Manajemen Armedia</span>
                  <a href="https://www.armedia.id" target="_blank" rel="noopener noreferrer" className="hover:text-white transition-colors">
                    www.armedia.id
                  </a>
                </div>
              </div>
            </motion.div>

          </div>
        </div>
      </div>
    </section>
  );
}




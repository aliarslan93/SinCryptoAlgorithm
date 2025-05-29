import numpy as np
import matplotlib.pyplot as plt

# Zaman ekseni
x = np.linspace(0, 4 * np.pi, 1000)
y = np.sin(x)

# Arka plan siyah
plt.figure(figsize=(12, 4), facecolor='black')
ax = plt.gca()
ax.set_facecolor('black')

# Renk segmentleri: yeşil (pozitif) ve kırmızı (negatif)
for i in range(1, len(x)):
    x_seg = x[i-1:i+1]
    y_seg = y[i-1:i+1]
   
    color = 'lime' if y[i] >= 0 else 'red'
    plt.plot(x_seg, y_seg, color=color, linewidth=2.5)

# 1=3 birleşim noktaları (yaklaşık: π, 2π, 3π)
merge_points = [np.pi, 2*np.pi, 3*np.pi]
for pt in merge_points:
    y_pt = np.sin(pt)
    plt.plot(pt, y_pt, 'o', color='white', markersize=10, markeredgewidth=0)
    plt.plot(pt, y_pt, 'o', color='white', alpha=0.3, markersize=30)  # glow efekti

# Temizleme
plt.axis('off')
plt.tight_layout()
plt.show()

#!/usr/bin/env python3
"""Compare two stylesheets ignoring comments and whitespace (both insignificant in CSS)."""
import re, sys

def norm(path):
    css = open(path, encoding='utf-8').read()
    css = re.sub(r'/\*.*?\*/', '', css, flags=re.S)   # comments carry no meaning
    css = re.sub(r'\s+', ' ', css)                     # nor does whitespace, outside strings
    return css.strip()

a, b = norm(sys.argv[1]), norm(sys.argv[2])

if a == b:
    print(f"IDENTICAL — {len(a)} significant characters")
    sys.exit(0)

print("DIFFERENT")
for i, (x, y) in enumerate(zip(a, b)):
    if x != y:
        print(f"  first difference at character {i}")
        print(f"  file 1: ...{a[max(0,i-60):i+60]}...")
        print(f"  file 2: ...{b[max(0,i-60):i+60]}...")
        break
else:
    longer, name = (a, sys.argv[1]) if len(a) > len(b) else (b, sys.argv[2])
    print(f"  one is longer; {name} has extra: ...{longer[min(len(a),len(b)):][:160]}...")
sys.exit(1)

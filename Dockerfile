# # Sử dụng image chính thức của Ubuntu 22.04
# FROM ubuntu:22.04 AS builder


# # Thiết lập biến môi trường để tránh các thông báo trong quá trình cài đặt
# ENV DEBIAN_FRONTEND=noninteractive


# # Cập nhật danh sách các gói và cài đặt các gói cần thiết
# RUN apt-get update && \
#     apt-get install -y python3 python3-pip git nano htop && \
#     apt-get clean && \
#     rm -rf /var/lib/apt/lists/*


# # Thiết lập thư mục làm việc trong container
# WORKDIR /_app_

# # Sao chép toàn bộ mã nguồn vào container
# COPY . /_app_


# # Cài đặt các gói yêu cầu
# RUN pip3 install --no-cache-dir --upgrade -r /_app_/requirements.txt


# # Xóa thư mục .venv nếu có
# RUN rm -rf /_app_/.venv || true


# # Mở cổng đúng theo run_api.py dùng
# EXPOSE 55013 


# # Lệnh để chạy ứng dụng
# CMD ["python3", "run_api.py"]

# Sử dụng image chính thức của Ubuntu 22.04
# FROM ubuntu:22.04 AS builder

# # Tránh các thông báo giao diện
# ENV DEBIAN_FRONTEND=noninteractive

# # Update và cài các gói cần thiết
# RUN apt-get update && \
#     apt-get install -y python3 python3-pip git nano htop gcc g++ make libglib2.0-0 libstdc++6 && \
#     apt-get clean && \
#     rm -rf /var/lib/apt/lists/*

# # Set thư mục làm việc
# WORKDIR /_app_

# # Copy source code
# COPY . /_app_

# # Cài requirements
# RUN pip3 install --no-cache-dir --upgrade pip
# RUN pip3 install --no-cache-dir --upgrade -r requirements.txt

# # Xóa .venv nếu có
# RUN rm -rf /_app_/.venv || true

# # Expose port đúng theo run_api.py
# EXPOSE 55013

# # Run app
# CMD ["python3", "run_api.py"]

FROM python:3.10-slim AS base

# Không ghi file .pyc
ENV PYTHONDONTWRITEBYTECODE=1
# Không lưu cache pip
ENV PIP_NO_CACHE_DIR=1

# Cài gói hệ thống tối thiểu
RUN apt-get update && apt-get install -y \
    gcc \
    g++ \
    build-essential \
    libglib2.0-0 \
    libstdc++6 \
    libopenblas-dev \
    git \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Tạo thư mục làm việc
WORKDIR /_app_

# Copy requirements và cài trước để dùng cache Docker layer
COPY . /_app_

# Upgrade pip và cài các thư viện Python
RUN pip3 install --no-cache-dir --upgrade pip
RUN pip3 install --no-cache-dir --upgrade -r requirements.txt


# Mở port
EXPOSE 55013

# Chạy app
CMD ["python", "run_api.py"]

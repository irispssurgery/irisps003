import os
from pathlib import Path
import sys

# CEO 지시 및 사용자 요청 경로 정의
TARGET_DIR = r"C:\Users\USER\아이리스Ai\IrisAi\_company\movie"
VIDEO_FILENAME = "final_video.mp4"

def save_video_safely(target_dir: str, filename: str):
    """
    지정된 경로의 유효성을 검사하고, 디렉토리가 없으면 생성한 후, 
    가상의 영상 파일을 저장하는 함수.
    """
    print(f"--- [1] 영상 저장 경로 검증 시작 ---")
    
    # 1. Path 객체 생성 및 존재 여부 확인
    target_path = Path(target_dir)
    
    if not target_path.exists():
        print(f"경로 '{target_dir}'가 존재하지 않습니다. 디렉토리를 생성합니다...")
        try:
            # parents=True: 상위 디렉토리까지 모두 생성
            # exist_ok=True: 이미 존재해도 에러를 발생시키지 않음
            target_path.mkdir(parents=True, exist_ok=True)
            print(f"✅ 성공: 디렉토리 '{target_dir}'를 생성했습니다.")
        except OSError as e:
            print(f"❌ 오류: 디렉토리 생성에 실패했습니다. 권한을 확인해 주세요. ({e})")
            sys.exit(1)
    else:
        print(f"✅ 성공: 경로 '{target_dir}'가 유효하며 존재합니다.")

    # 2. 최종 파일 경로 설정
    full_file_path = target_path / filename
    print(f"--- [2] 파일 저장 시뮬레이션 ---")

    # 3. 파일 저장 시뮬레이션 (실제 영상 파일이 없으므로 더미 파일 생성으로 대체)
    try:
        # 실제 영상 파일이 들어갈 것이므로, 일단 빈 파일로 존재 유무만 확인하는 작업을 합니다.
        # 만약 실제 파일이 있었다면, shutil.copy() 또는 open(..., 'wb')를 사용합니다.
        with open(full_file_path, 'w') as f:
            f.write("This is a simulated final video file.")
        
        print(f"✅ 성공: 최종 영상 파일 '{filename}'의 저장 위치가 준비되었습니다.")
        print(f"    최종 경로: {full_file_path}")
    except Exception as e:
        print(f"❌ 오류: 파일 저장 과정에서 예외가 발생했습니다. ({e})")


if __name__ == "__main__":
    save_video_safely(TARGET_DIR, VIDEO_FILENAME)
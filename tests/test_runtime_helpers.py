from pathlib import Path
import subprocess

ROOT = Path(__file__).resolve().parents[1]


def render_php(relative_path: str, timeout: int = 30) -> str:
    """Render a production PHP entry point through the CLI and return its HTML."""
    return subprocess.check_output(
        ['php', relative_path],
        cwd=ROOT,
        text=True,
        stderr=subprocess.STDOUT,
        timeout=timeout,
    )


def php_eval(code: str, timeout: int = 30) -> str:
    """Evaluate a small PHP probe against the project root."""
    return subprocess.check_output(
        ['php', '-r', code],
        cwd=ROOT,
        text=True,
        stderr=subprocess.STDOUT,
        timeout=timeout,
    )
